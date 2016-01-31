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
    public partial class FrmAccounts : Form
    {
        private int SortColumn = 0;
        private bool SortAscend = true;
        
        public FrmAccounts()
        {
            InitializeComponent();
            LstPeople.Columns[0].ImageIndex = 0;
        }

        private void BtnSearch_Click(object sender, EventArgs e)
        {
            Cursor = Cursors.WaitCursor;
            var payload = "action=GetUsers";
            if (TxtBadgeNumber.TextLength > 0)
                payload += "&badgeNumber=" + TxtBadgeNumber.Text;
            else if (TxtID.TextLength > 0)
                payload += "&id=" + TxtID.Text;
            else if (TxtLastName.TextLength > 0)
                payload += "&whereField=LastName&whereTerm=" + TxtLastName.Text + "&whereSimilar=true";
            else if (TxtEmail.TextLength > 0)
                payload += "&whereField=Email&whereTerm=" + TxtLastName.Text + "&whereSimilar=true";

            var data = Encoding.ASCII.GetBytes(payload);

            var request = WebRequest.Create(Program.URL + "/functions/userQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var users = JsonConvert.DeserializeObject<List<Person>>(results);

            LstPeople.BeginUpdate();
            LstPeople.Items.Clear();
            foreach (var user in users)
            {
                var item = new ListViewItem {Text = user.PeopleID.ToString()};
                item.SubItems.Add(user.FirstName);
                item.SubItems.Add(user.LastName);
                item.SubItems.Add(user.Email);
                if (user.Phone1 != null)
                    item.SubItems.Add(user.Phone1 + " (" + user.Phone1Type + ")");
                else
                    item.SubItems.Add("");
                item.Tag = user;
                LstPeople.Items.Add(item);
            }
            LstPeople.EndUpdate();
            Cursor = Cursors.Default;
        }

        private void LstPeople_ColumnClick(object sender, ColumnClickEventArgs e)
        {
            if (Math.Abs(SortColumn) == Math.Abs(e.Column))
            {
                SortAscend = !SortAscend;
                LstPeople.Columns[e.Column].ImageIndex = SortAscend ? 0 : 1;
            }
            else
            {
                LstPeople.Columns[SortColumn].ImageIndex = -1;
                LstPeople.Columns[SortColumn].TextAlign = LstPeople.Columns[SortColumn].TextAlign;
                SortAscend = true;
                SortColumn = e.Column;
                LstPeople.Columns[e.Column].ImageIndex = 0;
            }

            LstPeople.BeginUpdate();
            LstPeople.ListViewItemSorter = new ListViewItemComparer(e.Column, SortAscend);
            LstPeople.Sort();
            LstPeople.EndUpdate();
        }

        private void LstPeople_DoubleClick(object sender, EventArgs e)
        {
            if (LstPeople.SelectedItems.Count == 0) return;
            var dialog = new FrmAccountInfo((Person) LstPeople.SelectedItems[0].Tag);
            dialog.ShowDialog();
        }

        private void BtnClear_Click(object sender, EventArgs e)
        {
            TxtBadgeNumber.Text = "";
            TxtEmail.Text = "";
            TxtID.Text = "";
            TxtLastName.Text = "";
        }

        private void SearchField_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (e.KeyChar == (char)13)
                BtnSearch_Click(sender, new EventArgs());
        }
    }
}
