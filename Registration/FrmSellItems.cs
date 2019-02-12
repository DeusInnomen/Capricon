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
using CryptSharp;
using Newtonsoft.Json;

namespace Registration
{
    public partial class FrmSellItems : Form
    {
        private List<SaleItem> Items { get; set; }
        private Person Purchaser { get; set; }
        private Discount Discount { get; set; }
        private MagneticStripeScan Card { get; set; }
        private bool StripeFirstTry { get; set; }
        private String UniqueCode { get; set; }

        private Dictionary<int, Person> OneTimeRecipients { get; set; }
        private Dictionary<int, Person> Recipients { get; set; } 

        public FrmSellItems(Person recipient)
        {
            InitializeComponent();
            StripeFirstTry = true;
            UniqueCode = RandomString(6);

            OneTimeRecipients = new Dictionary<int, Person>();
            Recipients = new Dictionary<int, Person>();
            if (recipient.PeopleID != null)
                Recipients.Add((int) recipient.PeopleID, recipient);
            else if(recipient.OneTimeID != null)
                Recipients.Add((int) recipient.OneTimeID, recipient);

            Purchaser = recipient;
            CmbRecipient.Items.Add(recipient);
            CmbRecipient.Items.Add("Someone Else...");
            CmbRecipient.SelectedIndex = 0;

            var data = Encoding.ASCII.GetBytes("action=AvailableItems");
            var request = WebRequest.Create(Program.URL + "/functions/salesQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            request.Timeout = 20000;
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            Items = JsonConvert.DeserializeObject<List<SaleItem>>(results);

            LstItems.BeginUpdate();
            foreach (var saleItem in Items)
            {
                var item = new ListViewItem
                    {
                        Text = saleItem.Year + " " + saleItem.Description
                    };
                item.SubItems.Add(saleItem.Category);
                item.SubItems.Add(saleItem.Price.ToString("C"));
                item.SubItems.Add(saleItem.AvailableUntil.ToShortDateString());
                item.Tag = saleItem;
                LstItems.Items.Add(item);
            }
            LstItems.EndUpdate();
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }

        private void LstItems_DoubleClick(object sender, EventArgs e)
        {
            if (LstItems.SelectedItems.Count == 0) return;
            BtnAdd_Click(sender, e);
        }

        private void BtnAdd_Click(object sender, EventArgs e)
        {
            var saleItem = ((SaleItem)LstItems.SelectedItems[0].Tag).Clone();
            var recipient = (Person) CmbRecipient.SelectedItem;
            if (saleItem.CategoryID == 1)
            {
                var getName = new FrmGetDetail("Please provide the badge name to use:")
                {
                    DetailValue = recipient.BadgeName
                };
                if (getName.ShowDialog() == DialogResult.Cancel)
                {
                    getName.Close();
                    return;
                }
                saleItem.Details = getName.DetailValue;
                getName.Close();
            }

            if(recipient.PeopleID != null)
                saleItem.RecipientPeopleID = recipient.PeopleID;
            if (recipient.OneTimeID != null)
                saleItem.RecipientOneTimeID = recipient.OneTimeID;

            var item = new ListViewItem
            {
                Text = saleItem.Description
            };
            item.SubItems.Add(saleItem.Details);
            item.SubItems.Add(saleItem.Price.ToString("C"));
            item.Tag = saleItem;
            LstCart.Items.Add(item);
            CalculateTotal();
            BtnEditPrice.Enabled = true;
        }

        private void LstItems_SelectedIndexChanged(object sender, EventArgs e)
        {
            BtnAdd.Enabled = LstItems.SelectedItems.Count > 0;
        }

        private decimal CalculateTotal()
        {
            var total = (decimal) 0.00;
            foreach (ListViewItem item in LstCart.Items)
            {
                var saleItem = (SaleItem) item.Tag;
                total += saleItem.Price;
            }
            if (Discount != null)
            {
                var amount = (decimal) 0.00;
                if (Discount.Value != null)
                {
                    amount = (decimal)Discount.Value > total ? total : (decimal)Discount.Value;
                    var prices = new List<KeyValuePair<decimal, int>>();
                    foreach (ListViewItem item in LstCart.Items)
                    {
                        var saleItem = (SaleItem)item.Tag;
                        if (saleItem.CategoryID == 1 && saleItem.Price > 0)
                            prices.Add(new KeyValuePair<decimal, int>(saleItem.Price, LstCart.Items.IndexOf(item)));
                    }
                    if (prices.Count > 0)
                    {
                        var amountLeft = amount;
                        prices.Sort((a, b) => a.Key.CompareTo(b.Key)); // Lowest to Highest
                        for (var i = 0; i < prices.Count; i++)
                        {
                            var item = (SaleItem) LstCart.Items[prices[i].Value].Tag;
                            var discountAmount = amountLeft > prices[i].Key ? prices[i].Key : amountLeft;
                            item.Discount = discountAmount;
                            amountLeft -= discountAmount;
                        }
                    }

                }
                else if (Discount.Amount != null)
                {
                    foreach (ListViewItem item in LstCart.Items)
                    {
                        var saleItem = (SaleItem)item.Tag;
                        if (saleItem.Price > 0)
                            amount += saleItem.Price > (decimal)Discount.Amount ? (decimal)Discount.Amount : saleItem.Price;
                        ((SaleItem)item.Tag).Discount = saleItem.Price > (decimal)Discount.Amount ? (decimal)Discount.Amount : saleItem.Price;
                    }
                }
                else if (Discount.FreeBadges != null)
                {
                    var prices = new List<KeyValuePair<decimal, int>>();
                    foreach (ListViewItem item in LstCart.Items)
                    {
                        var saleItem = (SaleItem)item.Tag;
                        if (saleItem.CategoryID == 1 && saleItem.Price > 0)
                            prices.Add(new KeyValuePair<decimal, int>(saleItem.Price, LstCart.Items.IndexOf(item)));
                    }
                    if (prices.Count > 0)
                    {
                        prices.Sort((a, b) => b.Key.CompareTo(a.Key)); // Highest to Lowest
                        for (var i = 0; i < Discount.FreeBadges; i++)
                        {
                            amount += prices[i].Key;
                            ((SaleItem)LstCart.Items[prices[i].Value].Tag).Discount = prices[i].Key;
                        }
                    }
                }
                if (total - amount < 0) amount = total;
                LblDiscountAmount.Text = amount.ToString("C");
                total -= amount;
                LblDiscount.Visible = true;
                LblDiscountAmount.Visible = true;
            }
            else
            {
                LblDiscount.Visible = false;
                LblDiscountAmount.Visible = false;
                foreach (ListViewItem item in LstCart.Items)
                    ((SaleItem) item.Tag).Discount = 0;

            }
            LblAmountDue.Text = total.ToString("C");
            UpdatePurchaseButton();
            return total;
        }

        private void LstCart_SelectedIndexChanged(object sender, EventArgs e)
        {
            BtnRemoveItem.Enabled = LstCart.SelectedItems.Count > 0;
        }

        private void BtnRemoveItem_Click(object sender, EventArgs e)
        {
            while(LstCart.SelectedItems.Count > 0)
                LstCart.Items.Remove(LstCart.SelectedItems[0]);
            CalculateTotal();
            BtnEditPrice.Enabled = LstCart.Items.Count > 0;
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

        private void DoUpdatePurchaseButton(object sender, EventArgs e)
        {
            UpdatePurchaseButton();
        }

        private void BtnPurchase_Click(object sender, EventArgs e)
        {
            Cursor = Cursors.WaitCursor;
            string reference;
            string source;
            if (TabPaymentMethods.SelectedTab == TabCredit)
            {
                source = "Stripe";
                var dialog = new FrmProcessing
                    {
                        FirstTry = StripeFirstTry,
                        Person = Purchaser,
                        CardNumber = Card.CardNumber,
                        CardMonth = Card.ExpireMonth,
                        CardYear = Card.ExpireYear,
                        CardCVC = txtCVC.Text,
                        Amount = CalculateTotal(),
                        UniqueCode = UniqueCode
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

            var items = (from ListViewItem item in LstCart.Items select (SaleItem) item.Tag).ToList();
            var payload = "action=RecordPurchase&";
            if (Purchaser.PeopleID != null)
                payload += "purchaser=" + Purchaser.PeopleID;
            else
                payload += "onetime=" + Purchaser.OneTimeID;
            payload += "&reference=" + reference + "&source=" + source;
            if (TxtCode.TextLength > 0 && !TxtCode.Enabled) payload += "&code=" + TxtCode.Text;
            payload += "&items=" + JsonConvert.SerializeObject(items);

            var data = Encoding.ASCII.GetBytes(payload);
            var request = WebRequest.Create(Program.URL + "/functions/salesQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            request.Timeout = 20000;
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var webResponse = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(webResponse.GetResponseStream()).ReadToEnd();
            var response = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);
            if ((string) response["Result"] == "Success")
            {
                if (ChkPrintBadges.Checked && response.ContainsKey("BadgeNumbers"))
                {
                    var badgeNumbers =
                        JsonConvert.DeserializeObject<Dictionary<string, int>>((string) response["BadgeNumbers"]);
                    foreach (var item in items)
                    {
                        if (item.CategoryID == 1)
                        {
                            var recipient = item.RecipientOneTimeID != null
                                                ? OneTimeRecipients[(int) item.RecipientOneTimeID]
                                                : Recipients[(int) item.RecipientPeopleID];

                            var badge = new Badge
                                {
                                    BadgeName = item.Details,
                                    BadgeNumber = item.RecipientPeopleID != null
                                                      ? badgeNumbers[item.RecipientPeopleID.ToString()]
                                                      : badgeNumbers["_" + item.RecipientOneTimeID.ToString()],
                                    BadgeTypeID = item.TypeID,
                                    Description = item.Description,
                                    FirstName = recipient.FirstName,
                                    LastName = recipient.LastName,
                                    ParentName = recipient.ParentName,
                                    ParentContact = recipient.ParentContact
                                };
                            Badge.PrintBadge(badge);
                        }
                    }
                }

                MessageBox.Show("Your purchases have been recorded! Your reference number is \"" + reference + "\".",
                                "Purchase Complete", MessageBoxButtons.OK, MessageBoxIcon.Information);
                DialogResult = DialogResult.OK;
            }
            else
            {
                MessageBox.Show(
                    "An error occurred entering your purchases into the database: " + (string) response["Message"],
                    "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
            Cursor = Cursors.Default;
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

        private string RandomString(int length)
        {
            const string chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            return new string(Enumerable.Repeat(chars, length)
              .Select(s => s[Rand.Next(s.Length)]).ToArray());
        }

        private void BtnCode_Click(object sender, EventArgs e)
        {
            if (Discount == null)
            {
                if (TxtCode.TextLength == 0) return;
                var data = Encoding.ASCII.GetBytes("action=CheckCode&code=" + TxtCode.Text);
                var request = WebRequest.Create(Program.URL + "/functions/salesQuery.php");
                request.ContentLength = data.Length;
                request.ContentType = "application/x-www-form-urlencoded";
                request.Method = "POST";
                request.Timeout = 20000;
                using (var stream = request.GetRequestStream())
                    stream.Write(data, 0, data.Length);

                var responseJson = (HttpWebResponse) request.GetResponse();
                var results = new StreamReader(responseJson.GetResponseStream()).ReadToEnd();
                var response = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);

                if ((string) response["Result"] == "Success")
                {
                    var discount = new Discount();
                    if (response.ContainsKey("Discount"))
                        discount.Amount = Convert.ToDecimal(response["Discount"]);
                    if (response.ContainsKey("FreeBadges"))
                        discount.FreeBadges = Convert.ToInt32(response["FreeBadges"]);
                    if (response.ContainsKey("Value"))
                        discount.Value = Convert.ToDecimal(response["Value"]);
                    Discount = discount;
                    TxtCode.Enabled = false;
                    BtnCode.Text = "Clear Code";
                    CalculateTotal();
                }
                else
                {
                    MessageBox.Show((string)response["Reason"], "Cannot Use Code", MessageBoxButtons.OK,
                                    MessageBoxIcon.Exclamation);
                    Discount = null;
                }
            }
            else
            {
                Discount = null;
                TxtCode.Text = "";
                TxtCode.Enabled = true;
                BtnCode.Text = "Check Code";
                CalculateTotal();
            }
        }

        private void CmbRecipient_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (CmbRecipient.SelectedIndex == CmbRecipient.Items.Count - 1)
            {
                var select = new FrmSelectPerson();
                if (select.ShowDialog(this) == DialogResult.OK)
                {
                    var person = select.Person;
                    if (person.PeopleID == null && person.OneTimeID == null)
                        person.Save();

                    if (person.PeopleID != null)
                        Recipients.Add((int)person.PeopleID, person);
                    else if (person.OneTimeID != null)
                        Recipients.Add((int)person.OneTimeID, person);

                    CmbRecipient.Items.Insert(CmbRecipient.Items.Count - 1, person);
                    CmbRecipient.SelectedIndex = CmbRecipient.Items.Count - 2;
                }
                else
                    CmbRecipient.SelectedIndex = 0;
                select.Close();
            }
        }

        private void LstCart_DoubleClick(object sender, EventArgs e)
        {
            if (LstCart.SelectedItems.Count == 0) return;
            LstCart.Items.Remove(LstCart.SelectedItems[0]);
            CalculateTotal();
            BtnEditPrice.Enabled = LstCart.Items.Count > 0;
        }

        private void BtnAddManual_Click(object sender, EventArgs e)
        {
            var dialog = new FrmMiscCharge();
            if (dialog.ShowDialog() == DialogResult.Cancel) return;

            var saleItem = new SaleItem
                {
                    CategoryID = 3,
                    Category = "Miscellaneous Charge",
                    Description = "Miscellaneous Charge",
                    Details = dialog.ChargeDescription,
                    Price = dialog.ChargeAmount,
                    Year = DateTime.Now.Year
                };
            if (Purchaser.PeopleID != null)
                saleItem.RecipientPeopleID = Purchaser.PeopleID;
            if (Purchaser.OneTimeID != null)
                saleItem.RecipientOneTimeID = Purchaser.OneTimeID;

            var item = new ListViewItem {Text = "Miscellaneous Charge"};
            item.SubItems.Add(dialog.ChargeDescription);
            item.SubItems.Add(dialog.ChargeAmount.ToString("C"));
            item.Tag = saleItem;
            LstCart.Items.Add(item);
            CalculateTotal();

        }

        private void BtnEditPrice_Click(object sender, EventArgs e)
        {
            if (LstCart.SelectedItems.Count == 0) return;
            var price = ((SaleItem) LstCart.SelectedItems[0].Tag).Price;
            var dialog = new FrmEditItemPrice(price);
            if (dialog.ShowDialog() == DialogResult.Cancel) return;
            ((SaleItem) LstCart.SelectedItems[0].Tag).Price = dialog.NewAmount;
            LstCart.SelectedItems[0].SubItems[2].Text = dialog.NewAmount.ToString("C");
            CalculateTotal();
        }

        private void btnScanCard_Click(object sender, EventArgs e)
        {
            var getCard = new FrmCaptureCard();
            if(getCard.ShowDialog() == DialogResult.OK)
            {
                Card = getCard.Card;
                txtCardNumber.Text = Card.CardNumber.Substring(Card.CardNumber.Length - 4);
                txtExpires.Text = Card.ExpireMonth.PadLeft(2, Convert.ToChar("C")) + "/" + Card.ExpireYear;
                txtCVC.Enabled = true;
                UpdatePurchaseButton();                
            }            
        }
    }
}
