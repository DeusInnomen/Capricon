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
    public partial class FrmArtistCheckout : Form
    {
        private List<CheckoutItems> Items { get; set; }
        private ArtistPresence Presence { get; set; }
        private Artist Artist { get; set; }

        private int ArtShowSortColumn = 0;
        private bool ArtShowSortAscend = true;
        private int PrintShopSortColumn = 0;
        private bool PrintShopSortAscend = true;

        public FrmArtistCheckout(Artist artist, ArtistPresence presence)
        {
            InitializeComponent();
            Presence = presence;
            Artist = artist;
            CmbSelectedOption.SelectedIndex = 0;

            var data = Encoding.ASCII.GetBytes("action=GetArtistCheckout&id=" + Presence.ArtistAttendingID + "&year=" + Program.Year.ToString());
            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            Items = JsonConvert.DeserializeObject<List<CheckoutItems>>(results);

            decimal showTotal = 0;
            decimal shopTotal = 0;
            decimal feesDue = 0;
            decimal shippingCost = 0;

            foreach (var piece in Items.FindAll(i => !i.IsPrintShop))
            {
                var item = new ListViewItem()
                {
                    Text = piece.ShowNumber.ToString()
                };
                item.SubItems.Add(piece.LocationCode);
                item.SubItems.Add(piece.Title);
                item.SubItems.Add(piece.Media);
                if (piece.FinalSalePrice != null)
                {
                    item.SubItems.Add(((decimal) piece.FinalSalePrice).ToString("C"));
                    showTotal += (decimal)piece.FinalSalePrice;
                }
                else
                    item.SubItems.Add("Not Sold");
                item.SubItems.Add(piece.Claimed == 1 ? "Yes" : "No");
                item.Tag = piece;
                LstShowItems.Items.Add(item);
                if (piece.FeesPaid || piece.IsEAP || Artist.IsCharity) continue;
                if (piece.MinimumBid != null && piece.MinimumBid < 100)
                    feesDue += (decimal)0.5;
                else
                    feesDue += 1;
            }

            foreach (var piece in Items.FindAll(i => i.IsPrintShop))
            {
                var item = new ListViewItem()
                {
                    Text = piece.ShowNumber.ToString()
                };
                item.SubItems.Add(piece.LocationCode);
                item.SubItems.Add(piece.Title);
                item.SubItems.Add(piece.Media);
                item.SubItems.Add((piece.QuantitySent - piece.QuantitySold).ToString());
                item.SubItems.Add(((decimal)(piece.QuantitySold * piece.QuickSalePrice)).ToString("C"));
                item.SubItems.Add(piece.Claimed.ToString());
                item.Tag = piece;
                LstShopItems.Items.Add(item);
                shopTotal += (decimal)piece.QuantitySold * (decimal)piece.QuickSalePrice;
            }

            if (Presence.ShippingCost != null && Presence.ShippingPrepaid != null)
            {
                shippingCost = (decimal)Presence.ShippingCost - (decimal)Presence.ShippingPrepaid;
                LblShippingCost.Text = shippingCost.ToString("C");
                LblShippingCost.ForeColor = shippingCost >= 0 ? Color.Red : Color.Green;
                LblShippingCostText.Text = shippingCost >= 0 ? "Shipping Cost" : "Shipping Refund";
            }
            else
            {
                LblShippingCostText.Text = "";
                LblShippingCost.Text = "";
            }

            LblShowTotal.Text = showTotal.ToString("C");
            LblShopTotal.Text = shopTotal.ToString("C");
            var conShare = (showTotal + shopTotal)*(decimal) 0.1;
            if (Artist.IsCharity)
                conShare = (decimal) 0.0;
            LblConShare.Text = conShare.ToString("C");
            LblHangingFees.Text = feesDue.ToString("C");
            LblTotalOwed.Text = (showTotal + shopTotal - conShare - feesDue - shippingCost).ToString("C");
        }

        private void BtnPrintCheckout_Click(object sender, EventArgs e)
        {
            var dialog = new FrmArtistCheckoutSheet(Items);
            dialog.ShowDialog();
            dialog.Close();
            DialogResult = DialogResult.None;
        }

        private void LstShowItems_ColumnClick(object sender, ColumnClickEventArgs e)
        {
            if (Math.Abs(ArtShowSortColumn) == Math.Abs(e.Column))
            {
                ArtShowSortAscend = !ArtShowSortAscend;
                LstShowItems.Columns[e.Column].ImageIndex = ArtShowSortAscend ? 0 : 1;
            }
            else
            {
                LstShowItems.Columns[ArtShowSortColumn].ImageIndex = -1;
                LstShowItems.Columns[ArtShowSortColumn].TextAlign = LstShowItems.Columns[ArtShowSortColumn].TextAlign;
                ArtShowSortAscend = true;
                ArtShowSortColumn = e.Column;
                LstShowItems.Columns[e.Column].ImageIndex = 0;
            }

            LstShowItems.BeginUpdate();
            LstShowItems.ListViewItemSorter = new ListViewItemComparer(e.Column, ArtShowSortAscend);
            LstShowItems.Sort();
            LstShowItems.EndUpdate();
        }

        private void LstShopItems_ColumnClick(object sender, ColumnClickEventArgs e)
        {
            if (Math.Abs(PrintShopSortColumn) == Math.Abs(e.Column))
            {
                PrintShopSortAscend = !PrintShopSortAscend;
                LstShopItems.Columns[e.Column].ImageIndex = PrintShopSortAscend ? 0 : 1;
            }
            else
            {
                LstShopItems.Columns[PrintShopSortColumn].ImageIndex = -1;
                LstShopItems.Columns[PrintShopSortColumn].TextAlign = LstShopItems.Columns[PrintShopSortColumn].TextAlign;
                PrintShopSortAscend = true;
                PrintShopSortColumn = e.Column;
                LstShopItems.Columns[e.Column].ImageIndex = 0;
            }

            LstShopItems.BeginUpdate();
            LstShopItems.ListViewItemSorter = new ListViewItemComparer(e.Column, PrintShopSortAscend);
            LstShopItems.Sort();
            LstShopItems.EndUpdate();
        }


        private void BtnMarkAllClaimed_Click(object sender, EventArgs e)
        {
            foreach (ListViewItem item in LstShowItems.Items)
            {
                var shopItem = (CheckoutItems)item.Tag;
                if (shopItem.PurchaserID == null)
                {
                    shopItem.Claimed = 1;
                    item.SubItems[5].Text = "Yes";
                }
                item.SubItems[5].Text = "Yes";
            }
            foreach (ListViewItem item in LstShopItems.Items)
            {
                var shopItem = (CheckoutItems)item.Tag;
                shopItem.Claimed = shopItem.QuantitySent - shopItem.QuantitySold;
                item.SubItems[6].Text = shopItem.Claimed.ToString();
            }
        }

        private void BtnMarkUnsoldClaimed_Click(object sender, EventArgs e)
        {
            foreach (ListViewItem item in LstShowItems.Items)
            {
                var shopItem = (CheckoutItems)item.Tag;
                if (shopItem.PurchaserID == null)
                {
                    shopItem.Claimed = 1;
                    item.SubItems[5].Text = "Yes";
                }
            }
            foreach (ListViewItem item in LstShopItems.Items)
            {
                var shopItem = (CheckoutItems)item.Tag;
                shopItem.Claimed = shopItem.QuantitySent - shopItem.QuantitySold;
                item.SubItems[6].Text = shopItem.Claimed.ToString();
            }
        }

        private void BtnMarkSelected_Click(object sender, EventArgs e)
        {
            foreach (ListViewItem item in LstShowItems.SelectedItems)
            {
                var shopItem = (CheckoutItems)item.Tag;
                shopItem.Claimed = (CmbSelectedOption.SelectedIndex == 0 || CmbSelectedOption.SelectedIndex == 1 ? 1 : 0);
                item.SubItems[5].Text = shopItem.Claimed == 1 ? "Yes" : "No";
            }
            foreach (ListViewItem item in LstShopItems.SelectedItems)
            {
                var shopItem = (CheckoutItems)item.Tag;
                var remaining = shopItem.QuantitySent - shopItem.QuantitySold;
                if (CmbSelectedOption.SelectedIndex == 0)
                    shopItem.Claimed = remaining;
                if (CmbSelectedOption.SelectedIndex == 1) 
                    shopItem.Claimed += (shopItem.Claimed < remaining ? 1 : 0);
                if (CmbSelectedOption.SelectedIndex == 2)
                    shopItem.Claimed -= (shopItem.Claimed > 0 ? 1 : 0);
                if (CmbSelectedOption.SelectedIndex == 3)
                    shopItem.Claimed = 0;
                item.SubItems[6].Text = shopItem.Claimed.ToString();
            }
        }

        private void BtnSaveClaims_Click(object sender, EventArgs e)
        {
            this.Enabled = false;
            Cursor = Cursors.WaitCursor;
            Application.DoEvents();

            var payload = "action=MarkArtistPickup&id=" + Presence.ArtistAttendingID + "&values=";
            var ids = "";
            foreach (ListViewItem item in LstShowItems.Items)
                ids += ";" + ((CheckoutItems)item.Tag).ArtID.ToString() + "~" + ((CheckoutItems)item.Tag).Claimed.ToString();
            foreach (ListViewItem item in LstShopItems.Items)
                ids += ";" + ((CheckoutItems)item.Tag).ArtID.ToString() + "~" + ((CheckoutItems)item.Tag).Claimed.ToString();
            payload += ids.Substring(1);

            var data = Encoding.ASCII.GetBytes(payload);
            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var resultsJson = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var results = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(resultsJson);

            LstShowItems.SelectedItems.Clear();
            LstShopItems.SelectedItems.Clear();
            this.Enabled = true;
            Cursor = Cursors.Default;
        }
    }
}
