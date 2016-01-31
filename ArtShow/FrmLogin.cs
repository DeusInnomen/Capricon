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
    public partial class FrmLogin : Form
    {
        public FrmLogin()
        {
            InitializeComponent();
            for (int year = Program.Year; year >= 2014; year--)
                cmbYear.Items.Add(year);
            cmbYear.SelectedIndex = 0;
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

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;            
        }

        private void BtnLogin_Click(object sender, EventArgs e)
        {
            TxtEmail.Enabled = false;
            TxtPassword.Enabled = false;
            BtnLogin.Enabled = false;
            BtnCancel.Enabled = false;
            Cursor = Cursors.WaitCursor;

            var data = Encoding.ASCII.GetBytes("action=Login&app=ArtShow&email=" + TxtEmail.Text + "&pass=" + HttpUtility.UrlEncode(TxtPassword.Text));
            var request = WebRequest.Create(Program.URL + "/functions/authQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var responseJson = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var results = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(responseJson);

            if ((bool) results["success"])
            {
                Program.Year = Convert.ToInt32(cmbYear.Text);
                DialogResult = DialogResult.OK;
            }
            else
            {
                MessageBox.Show("Login Failed: " + (string) results["message"], "Login Failed", MessageBoxButtons.OK,
                                MessageBoxIcon.Exclamation);
                TxtEmail.Enabled = true;
                TxtPassword.Enabled = true;
                BtnLogin.Enabled = true;
                BtnCancel.Enabled = true;
                TxtPassword.Text = "";
                TxtPassword.Focus();
            }
            Cursor = Cursors.Default;
        }
    }
}
