using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Net;
using System.Text;
using System.Windows.Forms;
using Newtonsoft.Json;

namespace ArtShow
{
    public partial class FrmMain : Form
    {
        private FrmArtistSearch SearchForm { get; set; }
        private FrmGetPersonForSale SaleForm { get; set; }
        private FrmGetPersonForAuctionSales AuctionSaleForm { get; set; }
    
        public FrmMain()
        {
            InitializeComponent();
            this.Text += " -- " + Program.Year + " Data";
        }

        private void BtnLookup_Click(object sender, EventArgs e)
        {
            if (SearchForm != null)
                SearchForm.Focus();
            else
            {
                SearchForm = new FrmArtistSearch();
                SearchForm.Closed += SearchForm_Closed;
                SearchForm.Show();
            }
        }

        void SearchForm_Closed(object sender, EventArgs e)
        {
            SearchForm = null;
        }

        private void BtnSellItems_Click(object sender, EventArgs e)
        {
            if (SaleForm != null)
                SaleForm.Focus();
            else
            {
                SaleForm = new FrmGetPersonForSale();
                SaleForm.Closed += SaleFormOnClosed;
                SaleForm.Show();
            }
        }

        private void SaleFormOnClosed(object sender, EventArgs eventArgs)
        {
            SaleForm = null;
        }

        private void BtnPrintAllControlSheets_Click(object sender, EventArgs e)
        {
            var showPieces = GetAllInventory();
            var report = new FrmArtShowControl(showPieces);
            report.ShowDialog();
        }

        private void BtnPrintAllCheckoutSheets_Click(object sender, EventArgs e)
        {
            var data = Encoding.ASCII.GetBytes("action=GetArtistCheckout&year=" + Program.Year.ToString());
            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var Items = JsonConvert.DeserializeObject<List<CheckoutItems>>(results);

            var dialog = new FrmArtistCheckoutSheet(Items);
            dialog.ShowDialog();
        }

        private void BtnAuctionSales_Click(object sender, EventArgs e)
        {
            var showPieces = GetAllInventory();
            var auctionPieces = showPieces.FindAll(p => p.Auctioned);
            if (auctionPieces.Count == 0)
            {
                MessageBox.Show("Nothing has been indicated as going to auction yet.", "Nothing to Do",
                                MessageBoxButtons.OK,
                                MessageBoxIcon.Information);
                return;
            }
            var dialog = new FrmShowBidEntry(auctionPieces);
            dialog.ShowDialog();
            dialog.Close();
        }

        private void BtnAuctionReport_Click(object sender, EventArgs e)
        {
            var showPieces = GetAllInventory();
            var auctionPieces = showPieces.FindAll(p => p.Auctioned);
            if (auctionPieces.Count == 0)
            {
                MessageBox.Show("Nothing has been indicated as going to auction yet.", "Nothing to Do",
                                MessageBoxButtons.OK,
                                MessageBoxIcon.Information);
                return;
            }
            var report = new FrmAuctionReport(auctionPieces);
            report.ShowDialog();
        }

        private List<ArtShowItem> GetAllInventory()
        {
            var data = Encoding.ASCII.GetBytes("action=GetInventory&year=" + Program.Year);

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var inventory = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);
            return JsonConvert.DeserializeObject<List<ArtShowItem>>(inventory["showPieces"].ToString());

        }

        private void BtnCheckoutSales_Click(object sender, EventArgs e)
        {
            if (AuctionSaleForm != null)
                AuctionSaleForm.Focus();
            else
            {
                AuctionSaleForm = new FrmGetPersonForAuctionSales();
                AuctionSaleForm.Closed += AuctionSaleForm_Closed;
                AuctionSaleForm.Show();
            }
        }

        void AuctionSaleForm_Closed(object sender, EventArgs e)
        {
            AuctionSaleForm = null;
        }

        private void BtnAuctionResults_Click(object sender, EventArgs e)
        {
            var showPieces = GetAllInventory();
            var auctionPieces = showPieces.FindAll(p => p.FinalSalePrice != null && !p.Claimed);
            if (auctionPieces.Count == 0)
            {
                MessageBox.Show("Nothing has been listed as sold yet.", "Nothing to Do",
                                MessageBoxButtons.OK,
                                MessageBoxIcon.Information);
                return;
            }
            var report = new FrmAuctionPublicResults(auctionPieces);
            report.ShowDialog();
        }

        private void BtnExportInventory_Click(object sender, EventArgs e)
        {
            var dialog = new SaveFileDialog
            {
                Title = "Select location to save report...",
                CheckPathExists = true,
                CreatePrompt = false,
                DefaultExt = "csv",
                Filter = "Comma Separate Values (.csv)|*.csv",
                OverwritePrompt = true
            };
            if (dialog.ShowDialog() == DialogResult.Cancel)
                return;
            var filename = dialog.FileName;

            var data = Encoding.ASCII.GetBytes("action=GetInventoryList&year=" + Program.Year);
            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var inventoryList = JsonConvert.DeserializeObject<List<InventoryRecord>>(results);

            using(var sw = new StreamWriter(new FileStream(filename, FileMode.Create, FileAccess.Write, FileShare.ReadWrite)))
            {
                sw.WriteLine("ArtistNumber,DisplayName,LastName,Email,LocationCode,ArtShowPieces,PrintShopPieces");
                foreach (var inventory in inventoryList)
                    sw.WriteLine(inventory.ArtistNumber + ",\"" + inventory.DisplayName + "\",\"" + inventory.LastName + "\",\"" + inventory.Email + "\",\"" + inventory.LocationCode + "\"," +
                        inventory.ArtShowPieces + "," + inventory.PrintShopPieces);
            }
        }

        private void BtnAuctionPrivateResults_Click(object sender, EventArgs e)
        {
            var showPieces = GetAllInventory();
            var auctionPieces = showPieces.FindAll(p => p.FinalSalePrice != null && !p.Claimed);
            if (auctionPieces.Count == 0)
            {
                MessageBox.Show("Nothing has been listed as sold yet.", "Nothing to Do",
                                MessageBoxButtons.OK,
                                MessageBoxIcon.Information);
                return;
            }
            var report = new FrmAuctionResults(auctionPieces);
            report.ShowDialog();
        }

        private void BtnWaivedFeesReport_Click(object sender, EventArgs e)
        {
            var data = Encoding.ASCII.GetBytes("action=GetWaivedFeesReport&year=" + Program.Year);

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var records = JsonConvert.DeserializeObject<List<ArtistWithWaivedFees>>(results);

            var report = new FrmArtistsWithWaivedFees(records);
            report.ShowDialog();
        }

        private void BtnExportArtistSummary_Click(object sender, EventArgs e)
        {
            var dialog = new SaveFileDialog
            {
                Title = "Select location to save report...",
                CheckPathExists = true,
                CreatePrompt = false,
                DefaultExt = "csv",
                Filter = "Comma Separate Values (.csv)|*.csv",
                OverwritePrompt = true
            };
            if (dialog.ShowDialog() == DialogResult.Cancel)
                return;
            var filename = dialog.FileName;

            var data = Encoding.ASCII.GetBytes("action=GetArtistsSummaryList&year=" + Program.Year);
            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var records = JsonConvert.DeserializeObject<List<ArtistSummary>>(results);

            using (var sw = new StreamWriter(new FileStream(filename, FileMode.Create, FileAccess.Write, FileShare.ReadWrite)))
            {
                sw.WriteLine("DisplayName,LegalName,Address1,Address2,City,State,ZipCode,Country,StartDate,EndDate,Location");
                foreach (var record in records)
                    sw.WriteLine("\"" + record.DisplayName + "\",\"" + record.LegalName + "\",\"" + record.Address1 + "\",\"" + record.Address2 + "\",\"" + record.City + "\",\"" + 
                        record.State + "\",\"" + record.ZipCode + "\",\"" + record.Country + "\",\"" + record.StartDate.ToShortDateString() + "\",\"" + 
                        record.EndDate.ToShortDateString() + "\",\"" + record.Location + "\"");
            }
        }

        private void BtnUnsoldAuction_Click(object sender, EventArgs e)
        {
            var showPieces = GetAllInventory();
            var unsoldPieces = showPieces.FindAll(p => !p.Auctioned && !p.Claimed);
            if (unsoldPieces.Count == 0)
            {
                MessageBox.Show("All inventory has been accounted for.", "Nothing to Do",
                                MessageBoxButtons.OK,
                                MessageBoxIcon.Information);
                return;
            }
            var report = new FrmAuctionSweep(unsoldPieces);
            report.ShowDialog();

        }
    }
}
