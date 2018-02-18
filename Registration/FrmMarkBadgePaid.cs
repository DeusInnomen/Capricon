using Newtonsoft.Json;
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

namespace Registration
{
    public partial class FrmMarkBadgePaid : Form
    {
        private Badge ThisBadge { get; set; }
        public FrmMarkBadgePaid(Badge badge)
        {
            InitializeComponent();
            ThisBadge = badge;
            TxtBadgeName.Text = badge.BadgeName;
            TxtBadgeHolder.Text = badge.Name;
            TxtBadgeNumber.Text = badge.BadgeNumber.ToString();
        }

        private void btnMarkAsPaid_Click(object sender, EventArgs e)
        {
            if(TxtCheckNumber.TextLength == 0)
                return;

            Cursor = Cursors.WaitCursor;
            var data = Encoding.ASCII.GetBytes("action=ModifyBadge&badgeAction=ApproveBadge&badgeID=" + ThisBadge.BadgeID +
                "&checkNum=" + TxtCheckNumber.Text);
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
            Cursor = Cursors.Default;
            if ((string)results["result"] == "Success")
            {
                MessageBox.Show("The badge has been successfully marked as 'Paid'.");
                DialogResult = DialogResult.OK;
            }
            else
                MessageBox.Show("An error occurred. Please contact the IT Director.");
        }
    }
}
