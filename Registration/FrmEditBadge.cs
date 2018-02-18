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

namespace Registration
{
    public partial class FrmEditBadge : Form
    {
        public Badge Badge { get; set; }

        public FrmEditBadge(Badge badge)
        {
            InitializeComponent();
            Badge = badge;

            TxtBadgeNumber.Text = badge.BadgeNumber.ToString();
            TxtBadgeName.Text = badge.BadgeName;
            TxtBadgeHolder.Text = badge.Name;

            btnMarkAsPaid.Enabled = badge.Status != "Paid";
        }

        protected override CreateParams CreateParams
        {
            get
            {
                // Disable the Close button on the form.
                const int noCloseButton = 0x200;
                var cp = base.CreateParams;
                cp.ClassStyle |= noCloseButton;
                return cp;
            }
        }

        private void BtnClose_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.OK;
        }

        private void BtnSave_Click(object sender, EventArgs e)
        {
            Cursor = Cursors.WaitCursor;
            Badge.BadgeName = TxtBadgeName.Text;
            var data = Encoding.ASCII.GetBytes("action=ModifyBadge&badgeAction=Save&badgeID=" + Badge.BadgeID +
                "&badgeName=" + HttpUtility.UrlEncode(Badge.BadgeName));
            var request = WebRequest.Create(Program.URL + "/functions/userQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            request.Timeout = 20000;
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var responseJson = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var results = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(responseJson);
            if ((string) results["result"] == "Success")
                LblMessage.Text = "The badge has been successfully updated.";
            Cursor = Cursors.Default;
        }

        private void BtnDelete_Click(object sender, EventArgs e)
        {
            if (MessageBox.Show(
                "This action cannot be undone! Are you absolutely sure you wish to delete the badge \"" +
                Badge.BadgeName + "\" held by " + Badge.Name + "?", "Confirmation Required", MessageBoxButtons.YesNo,
                MessageBoxIcon.Exclamation, MessageBoxDefaultButton.Button2) == DialogResult.Yes)
            {
                Cursor = Cursors.WaitCursor;
                var data = Encoding.ASCII.GetBytes("action=ModifyBadge&badgeAction=Delete&badgeID=" + Badge.BadgeID);
                var request = WebRequest.Create(Program.URL + "/functions/userQuery.php");
                request.ContentLength = data.Length;
                request.ContentType = "application/x-www-form-urlencoded";
                request.Method = "POST";
                request.Timeout = 20000;
                using (var stream = request.GetRequestStream())
                    stream.Write(data, 0, data.Length);

                var response = (HttpWebResponse)request.GetResponse();
                var responseJson = new StreamReader(response.GetResponseStream()).ReadToEnd();
                var results = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(responseJson);
                if ((string) results["result"] == "Success")
                {
                    LblMessage.Text = "The badge has been successfully deleted.";
                    BtnDelete.Enabled = false;
                    BtnSave.Enabled = false;
                    BtnTransfer.Enabled = false;
                    TxtBadgeName.Enabled = false;
                    Badge = null;
                }
                Cursor = Cursors.Default;
            }
        }

        private void BtnTransfer_Click(object sender, EventArgs e)
        {
            var dialog = new FrmTransferBadge(Badge);
            if (dialog.ShowDialog() == DialogResult.OK)
            {
                Badge = dialog.TransferBadge;
                TxtBadgeHolder.Text = Badge.Name;
                TxtBadgeName.Text = Badge.BadgeName;
            }
            dialog.Close();
        }

        private void btnMarkAsPaid_Click(object sender, EventArgs e)
        {
            var dialog = new FrmMarkBadgePaid(Badge);
            if (dialog.ShowDialog() == DialogResult.OK)
            {
                Badge.Status = "Paid";
                btnMarkAsPaid.Enabled = false;
            }
            dialog.Close();
        }
    }
}
