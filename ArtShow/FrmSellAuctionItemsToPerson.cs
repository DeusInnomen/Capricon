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
    public partial class FrmSellAuctionItemsToPerson : Form
    {
        private List<ArtShowItem> Items { get; set; }
        private decimal TotalDue { get; set; }
        private PersonPickup Person { get; set; }
        private MagneticStripeScan Card { get; set; }
        private bool StripeFirstTry { get; set; }

        private int SortColumn = 0;
        private bool SortAscend = true;

        public FrmSellAuctionItemsToPerson(PersonPickup person)
        {
            InitializeComponent();
            Person = person;
            StripeFirstTry = true;
            Text = "Items Won by " + Person.Name;

            var payload = "action=GetItemsForPickup&id=" + Person.BadgeID + "&Year=" + Program.Year.ToString();
            var data = Encoding.ASCII.GetBytes(payload);

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            Items = JsonConvert.DeserializeObject<List<ArtShowItem>>(results);

            TotalDue = 0;
            foreach (var showItem in Items)
            {
                var item = new ListViewItem {Text = showItem.LocationCode};
                item.SubItems.Add(showItem.ShowNumber.ToString());
                item.SubItems.Add(showItem.Title);
                item.SubItems.Add(showItem.ArtistDisplayName);
                item.SubItems.Add(((decimal) showItem.FinalSalePrice).ToString("C"));
                item.Tag = showItem;
                LstItems.Items.Add(item);
                TotalDue += (decimal)showItem.FinalSalePrice;
            }
            LblAmountDue.Text = TotalDue.ToString("C");
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }

        private void CheckPurchaseButton(object sender, EventArgs e)
        {
            if (TabPaymentMethods.SelectedTab == TabCredit)
                BtnPurchase.Enabled = Card != null && Card.Valid && txtCVC.TextLength >= 3;
            else if (TabPaymentMethods.SelectedTab == TabCheck)
                BtnPurchase.Enabled = TxtCheckNumber.TextLength > 0;
            else
                BtnPurchase.Enabled = true;
        }

        private void BtnPurchase_Click(object sender, EventArgs e)
        {
            string reference;
            string source;
            if (TabPaymentMethods.SelectedTab == TabCredit)
            {
                source = "Stripe";
                var dialog = new FrmProcessing
                {
                    Description = "Auction Pieces",
                    FirstTry = StripeFirstTry,
                    PayeeName = Person.Name,
                    CardNumber = Card.CardNumber,
                    CardMonth = Card.ExpireMonth,
                    CardYear = Card.ExpireYear,
                    CardCVC = txtCVC.Text,
                    Amount = TotalDue
                };

                dialog.ShowDialog();

                if (dialog.Charge != null)
                    reference = dialog.Charge.Id;
                else
                {
                    StripeFirstTry = false;
                    MessageBox.Show("The transaction was declined: " + dialog.Error.Message, "Charge Failed",
                                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                    return;
                }
            }
            else
            {
                reference = GetRandomHexNumber(13);
                if (TabPaymentMethods.SelectedTab == TabCash)
                    source = "Cash";
                else if (TabPaymentMethods.SelectedTab == TabCheck)
                {
                    source = "Check";
                    reference += "_#" + TxtCheckNumber.Text;
                }
                else
                    source = "Waived";
            }

            var payload = "action=SellAuctionItems&Year=" + Program.Year.ToString() + "&total=" + TotalDue + "&id=" + 
                Person.BadgeID + "&pieces=" + Items.Count + "&source=" + source + "&reference=" + reference;

            var data = Encoding.ASCII.GetBytes(payload);
            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var webResponse = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(webResponse.GetResponseStream()).ReadToEnd();
            var response = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);
            if ((string)response["Result"] == "Success")
            {
                MessageBox.Show("Your purchases have been recorded! Your reference number is \"" + reference + "\".",
                                "Purchase Complete", MessageBoxButtons.OK, MessageBoxIcon.Information);
                var receipt = new FrmAuctionReceipt(Person, Items, source, reference);
                receipt.ShowDialog();
                DialogResult = DialogResult.OK;
            }
            else
            {
                MessageBox.Show(
                    "An error occurred processing your auction sales with the database: " + (string)response["Message"],
                    "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                DialogResult = DialogResult.None;
            }
        }

        static readonly Random Rand = new Random();
        public static string GetRandomHexNumber(int digits)
        {
            var buffer = new byte[digits / 2];
            Rand.NextBytes(buffer);
            var result = String.Concat(buffer.Select(x => x.ToString("X2")).ToArray());
            if (digits % 2 == 0)
                return result;
            return result + Rand.Next(16).ToString("X");
        }

        private void BtnPrintSummary_Click(object sender, EventArgs e)
        {
            var dialog = new FrmAuctionReport(Items);
            dialog.ShowDialog();
            dialog.Close();
            DialogResult = DialogResult.None;
        }

        private void btnScanCard_Click(object sender, EventArgs e)
        {
            var getCard = new FrmCaptureCard();
            if (getCard.ShowDialog() == DialogResult.OK)
            {
                Card = getCard.Card;
                txtCardNumber.Text = Card.CardNumber.Substring(Card.CardNumber.Length - 4);
                txtExpires.Text = Card.ExpireMonth.PadLeft(2, Convert.ToChar("C")) + "/" + Card.ExpireYear;
                txtCVC.Enabled = true;
                CheckPurchaseButton(sender, e);
            }
        }

        private void LstItems_ColumnClick(object sender, ColumnClickEventArgs e)
        {
            if (Math.Abs(SortColumn) == Math.Abs(e.Column))
            {
                SortAscend = !SortAscend;
                LstItems.Columns[e.Column].ImageIndex = SortAscend ? 0 : 1;
            }
            else
            {
                LstItems.Columns[SortColumn].ImageIndex = -1;
                LstItems.Columns[SortColumn].TextAlign = LstItems.Columns[SortColumn].TextAlign;
                SortAscend = true;
                SortColumn = e.Column;
                LstItems.Columns[e.Column].ImageIndex = 0;
            }

            LstItems.BeginUpdate();
            LstItems.ListViewItemSorter = new ListViewItemComparer(e.Column, SortAscend);
            LstItems.Sort();
            LstItems.EndUpdate();
        }
    }
}
