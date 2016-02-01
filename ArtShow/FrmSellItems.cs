using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Net;
using System.Text;
using System.Web;
using System.Windows.Forms;
using Newtonsoft.Json;

namespace ArtShow
{
    public partial class FrmSellItems : Form
    {
        private Person Purchaser { get; set; }
        private MagneticStripeScan Card { get; set; }
        private List<PrintShopItem> Items { get; set; }
        private bool StripeFirstTry { get; set; }

        private int ItemsSortColumn = 0;
        private bool ItemsSortAscend = true;
        private int CartSortColumn = 0;
        private bool CartSortAscend = true;

        public FrmSellItems(Person purchaser)
        {
            InitializeComponent();
            Purchaser = purchaser;
            StripeFirstTry = true;

            var data = Encoding.ASCII.GetBytes("action=GetPrintShopList&Year=" + Program.Year.ToString());
            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            Items = JsonConvert.DeserializeObject<List<PrintShopItem>>(results);

            LstItems.BeginUpdate();
            foreach (var shopItem in Items)
            {
                var item = new ListViewItem
                {
                    Text = shopItem.ShowNumber.ToString()
                };
                item.SubItems.Add(shopItem.Title);
                item.SubItems.Add(shopItem.ArtistName);
                item.SubItems.Add(shopItem.Price.ToString("C"));
                item.SubItems.Add((shopItem.QuantitySent - shopItem.QuantitySold).ToString());
                if (shopItem.QuantitySent - shopItem.QuantitySold == 0)
                {
                    item.ForeColor = Color.Red;
                    item.BackColor = Color.LightGray;
                }
                item.Tag = shopItem;
                LstItems.Items.Add(item);
            }
            LstItems.EndUpdate();
        }

        private void LstItems_ColumnClick(object sender, ColumnClickEventArgs e)
        {
            if (Math.Abs(ItemsSortColumn) == Math.Abs(e.Column))
            {
                ItemsSortAscend = !ItemsSortAscend;
                LstItems.Columns[e.Column].ImageIndex = ItemsSortAscend ? 0 : 1;
            }
            else
            {
                LstItems.Columns[ItemsSortColumn].ImageIndex = -1;
                LstItems.Columns[ItemsSortColumn].TextAlign = LstItems.Columns[ItemsSortColumn].TextAlign;
                ItemsSortAscend = true;
                ItemsSortColumn = e.Column;
                LstItems.Columns[e.Column].ImageIndex = 0;
            }

            LstItems.BeginUpdate();
            LstItems.ListViewItemSorter = new ListViewItemComparer(e.Column, ItemsSortAscend);
            LstItems.Sort();
            LstItems.EndUpdate();
        }

        private void LstItems_DoubleClick(object sender, EventArgs e)
        {
            if (LstItems.SelectedItems.Count == 0) return;
            BtnAdd_Click(sender, e);
        }

        private void BtnAdd_Click(object sender, EventArgs e)
        {
            var shopItem = ((PrintShopItem)LstItems.SelectedItems[0].Tag).Clone();

            // Check to make sure we don't have the current maximum quantity in the cart.
            var count = 0;
            foreach (ListViewItem cartItem in LstCart.Items)
                if (((PrintShopItem)cartItem.Tag).ArtID == shopItem.ArtID) count++;

            if (shopItem.QuantitySent - shopItem.QuantitySold == count) return;

            var item = new ListViewItem
            {
                Text = shopItem.Title
            };
            item.SubItems.Add(shopItem.ArtistName);
            item.SubItems.Add(shopItem.Price.ToString("C"));
            item.Tag = shopItem;
            LstCart.Items.Add(item);
            CalculateTotal();
            BtnClearCart.Enabled = true;
        }

        private void LstItems_SelectedIndexChanged(object sender, EventArgs e)
        {
            BtnAdd.Enabled = LstItems.SelectedItems.Count > 0;
        }

        private void BtnRemoveItem_Click(object sender, EventArgs e)
        {
            while (LstCart.SelectedItems.Count > 0)
                LstCart.Items.Remove(LstCart.SelectedItems[0]);
            CalculateTotal();
            BtnClearCart.Enabled = LstCart.Items.Count > 0;
        }

        private decimal CalculateTotal()
        {
            decimal total = LstCart.Items.Cast<ListViewItem>().Sum(cartItem => ((PrintShopItem) cartItem.Tag).Price);
            LblAmountDue.Text = total.ToString("C");
            UpdatePurchaseButton();
            return total;
        }

        private void BtnClearCart_Click(object sender, EventArgs e)
        {
            LstCart.Items.Clear();
            CalculateTotal();
            BtnClearCart.Enabled = false;
        }

        private void DoUpdatePurchaseButton(object sender, EventArgs e)
        {
            UpdatePurchaseButton();
        }

        private void UpdatePurchaseButton()
        {
            var enabled = false;
            if (LstCart.Items.Count > 0)
                if (TabPaymentMethods.SelectedTab == TabCash)
                    enabled = true;
                else if (TabPaymentMethods.SelectedTab == TabCheck)
                    enabled = TxtCheckNumber.TextLength > 0;
                else
                    enabled = Card != null && Card.Valid && txtCVC.TextLength >= 3;
            BtnPurchase.Enabled = enabled;
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }

        private void BtnPurchase_Click(object sender, EventArgs e)
        {
            Cursor = Cursors.WaitCursor;
            string reference;
            string source;
            var total = CalculateTotal();
            if (TabPaymentMethods.SelectedTab == TabCredit)
            {
                source = "Stripe";
                var dialog = new FrmProcessing
                {
                    FirstTry = StripeFirstTry,
                    Person = Purchaser,
                    Description = "Print Shop Purchases",
                    CardNumber = Card.CardNumber,
                    CardMonth = Card.ExpireMonth,
                    CardYear = Card.ExpireYear,
                    CardCVC = txtCVC.Text,
                    Amount = total
                };

                dialog.ShowDialog();

                if (dialog.Charge != null)
                    reference = dialog.Charge.Id;
                else
                {
                    StripeFirstTry = false;
                    Cursor = Cursors.Default;
                    MessageBox.Show("The transaction was declined: " + dialog.Error.Message, "Charge Failed",
                                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                    return;
                }
            }
            else
            {
                source = TabPaymentMethods.SelectedTab == TabCash ? "Cash" : "Check";
                reference = GetRandomHexNumber(13);
                if (TabPaymentMethods.SelectedTab == TabCheck)
                    reference += "_#" + TxtCheckNumber.Text;
            }

            var items = (from ListViewItem item in LstCart.Items select (PrintShopItem)item.Tag).ToList();
            var payload = "action=RecordPrintShopSales&Year=" + Program.Year.ToString() + "&total=" + total;
            if (Purchaser.PeopleID != null)
                payload += "&purchaser=" + Purchaser.PeopleID;
            else
                payload += "&onetime=" + Purchaser.OneTimeID;
            payload += "&reference=" + reference + "&source=" + source;
            payload += "&items=" + HttpUtility.UrlEncode(JsonConvert.SerializeObject(items));

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
                var receipt = new FrmShopReceipt(Purchaser, items, source, reference);
                receipt.ShowDialog();
                DialogResult = DialogResult.OK;
            }
            else
            {
                MessageBox.Show(
                    "An error occurred entering your purchases into the database: " + (string)response["Message"],
                    "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
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

        private void LstCart_DoubleClick(object sender, EventArgs e)
        {
            if (LstCart.SelectedItems.Count == 0) return;
            LstCart.Items.Remove(LstCart.SelectedItems[0]);
            CalculateTotal();
            BtnClearCart.Enabled = LstCart.Items.Count > 0;
        }

        private void LstCart_SelectedIndexChanged(object sender, EventArgs e)
        {
            BtnRemoveItem.Enabled = LstCart.SelectedItems.Count > 0;
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
                UpdatePurchaseButton();
            }
        }

        private void LstCart_ColumnClick(object sender, ColumnClickEventArgs e)
        {
            if (Math.Abs(CartSortColumn) == Math.Abs(e.Column))
            {
                CartSortAscend = !CartSortAscend;
                LstCart.Columns[e.Column].ImageIndex = CartSortAscend ? 0 : 1;
            }
            else
            {
                LstCart.Columns[CartSortColumn].ImageIndex = -1;
                LstCart.Columns[CartSortColumn].TextAlign = LstCart.Columns[CartSortColumn].TextAlign;
                CartSortAscend = true;
                CartSortColumn = e.Column;
                LstCart.Columns[e.Column].ImageIndex = 0;
            }

            LstCart.BeginUpdate();
            LstCart.ListViewItemSorter = new ListViewItemComparer(e.Column, CartSortAscend);
            LstCart.Sort();
            LstCart.EndUpdate();
        }

        private void txtSearch_TextChanged(object sender, EventArgs e)
        {
            LstItems.BeginUpdate();
            LstItems.Items.Clear();
            var filtered = txtSearch.TextLength > 0 ?
                Items.Where(i => i.Title.ToLower().Contains(txtSearch.Text.ToLower())).ToList() :
                Items;
            foreach (var shopItem in filtered)
            {
                var item = new ListViewItem
                {
                    Text = shopItem.ShowNumber.ToString()
                };
                item.SubItems.Add(shopItem.Title);
                item.SubItems.Add(shopItem.ArtistName);
                item.SubItems.Add(shopItem.Price.ToString("C"));
                item.SubItems.Add((shopItem.QuantitySent - shopItem.QuantitySold).ToString());
                if (shopItem.QuantitySent - shopItem.QuantitySold == 0)
                {
                    item.ForeColor = Color.Red;
                    item.BackColor = Color.LightGray;
                }
                item.Tag = shopItem;
                LstItems.Items.Add(item);
            }
            LstItems.EndUpdate();
        }

        private void btnClearFilter_Click(object sender, EventArgs e)
        {
            txtSearch.Text = "";

        }
    }
}
