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
    public partial class FrmHangingFees : Form
    {
        private decimal FeesDue { get; set; }
        private MagneticStripeScan Card { get; set; }
        private ArtistPresence Presence { get; set; }

        public FrmHangingFees(ArtistPresence presence, Artist artist, decimal fees)
        {
            InitializeComponent();
            Presence = presence;
            CmbPayee.Items.Add(artist.LegalName);
            if (!string.IsNullOrEmpty(Presence.AgentName))
                CmbPayee.Items.Add(Presence.AgentName);
            CmbPayee.SelectedIndex = 0;

            FeesDue = fees;
            LblFees.Text = fees.ToString("C");

            if (fees != 0) return;
            TabMethods.SelectedTab = TabWaive;
            TabMethods.Enabled = false;
            BtnSubmit.Enabled = false;
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }

        private void BtnSubmit_Click(object sender, EventArgs e)
        {
            string reference;
            string source;
            if (TabMethods.SelectedTab == TabCredit)
            {
                source = "Stripe";
                var dialog = new FrmProcessing
                    {
                        Description = "Hanging Fees",
                        PayeeName = CmbPayee.SelectedItem.ToString(),
                        CardNumber = Card.CardNumber,
                        CardMonth = Card.ExpireMonth,
                        CardYear = Card.ExpireYear,
                        CardCVC = txtCVC.Text,
                        Amount = FeesDue
                    };

                dialog.ShowDialog();

                if (dialog.Charge != null)
                    reference = dialog.Charge.Id;
                else
                {
                    MessageBox.Show("The transaction was declined: " + dialog.Error.Message, "Charge Failed",
                                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                    return;
                }
            }
            else
            {
                reference = GetRandomHexNumber(13);
                if (TabMethods.SelectedTab == TabCash)
                    source = "Cash";
                else if (TabMethods.SelectedTab == TabCheck)
                {
                    source = "Check";
                    reference += "_#" + TxtCheckNumber.Text;
                }
                else
                    source = "Waived";
            }

            var payload = "action=PayHangingFees&fees=" + FeesDue + "&id=" + Presence.ArtistAttendingID + "&Year=" + 
                Program.Year.ToString() + "&source=" + source + "&reference=" + reference;

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
            if ((string) response["Result"] == "Success")
            {
                MessageBox.Show("All hanging fees have been settled.", "Success", MessageBoxButtons.OK,
                                MessageBoxIcon.Information);
                DialogResult = DialogResult.OK;
            }
            else
            {
                MessageBox.Show(
                    "An error occurred processing your hanging fees with the database: " + (string)response["Message"],
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

        private void SetSubmitButton(object sender, EventArgs e)
        {
            if (FeesDue == 0) return;
            if (TabMethods.SelectedTab == TabCredit)
                BtnSubmit.Enabled = Card != null && Card.Valid && txtCVC.TextLength >= 3;
            else if (TabMethods.SelectedTab == TabCheck)
                BtnSubmit.Enabled = TxtCheckNumber.TextLength > 0;
            else
                BtnSubmit.Enabled = true;
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
                SetSubmitButton(sender, e);
            }   

        }
    }
}
