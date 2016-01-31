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

namespace Registration
{
    public partial class FrmMain : Form
    {
        private FrmAccounts AccountForm { get; set; }
        private FrmBadges BadgeForm { get; set; }
        private FrmGetPersonForSale SaleForm { get; set; }

        public FrmMain()
        {
            InitializeComponent();
        }

        private void BtnLookup_Click(object sender, EventArgs e)
        {
            if(AccountForm != null)
                AccountForm.Show();
            else
            {
                AccountForm = new FrmAccounts();
                AccountForm.Closed += AccountFormOnClosed;
                AccountForm.Show();
            }
        }

        private void AccountFormOnClosed(object sender, EventArgs eventArgs)
        {
            AccountForm = null;
        }

        private void BtnPrintBadges_Click(object sender, EventArgs e)
        {
            if (BadgeForm != null)
                BadgeForm.Show();
            else
            {
                BadgeForm = new FrmBadges();
                BadgeForm.Closed += BadgeFormOnClosed;
                BadgeForm.Show();
            }
        }

        private void BadgeFormOnClosed(object sender, EventArgs eventArgs)
        {
            BadgeForm = null;
        }

        private void BtnSellItems_Click(object sender, EventArgs e)
        {
            if (SaleForm != null)
                SaleForm.Show();
            else
            {
                SaleForm = new FrmGetPersonForSale();
                SaleForm.Closed += SaleForm_Closed;
                SaleForm.Show();
            }
        }

        void SaleForm_Closed(object sender, EventArgs e)
        {
            SaleForm = null;
        }

        private void BtnPrintSheetsStaff_Click(object sender, EventArgs e)
        {
            OpenSigninSheetReport("Staff");
        }

        private void BtnPrintSheetsAttendees_Click(object sender, EventArgs e)
        {
            OpenSigninSheetReport("Attendee");
        }

        private void OpenSigninSheetReport(string recordType)
        {
            var data = Encoding.ASCII.GetBytes("action=GetPickupReportData&type=" + recordType);

            var request = WebRequest.Create(Program.URL + "/functions/userQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var badges = JsonConvert.DeserializeObject<List<GeneratedBadge>>(results);

            var dialog = new FrmSigninReport(badges);
            dialog.ShowDialog();
        }
    }
}
