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
    public partial class FrmArtistSearch : Form
    {
        private int SortColumn = 1;
        private bool SortAscend = true;
        
        public FrmArtistSearch()
        {
            InitializeComponent();
            LstPeople.Columns[1].ImageIndex = 0;
            ChkOnlyInventory.Checked = Program.WithInventoryOnly;
        }

        private void BtnSearch_Click(object sender, EventArgs e)
        {
            var payload = "action=GetArtists";
            if (TxtDisplayName.TextLength > 0)
                payload += "&whereField=DisplayName&whereTerm=" + TxtDisplayName.Text + "&whereSimilar=true";
            else if (TxtLastName.TextLength > 0)
                payload += "&whereField=LastName&whereTerm=" + TxtLastName.Text + "&whereSimilar=true";
            else if (TxtEmail.TextLength > 0)
                payload += "&whereField=Email&whereTerm=" + TxtEmail.Text + "&whereSimilar=true";
            if (ChkOnlyInventory.Checked)
                payload += "&withInventory=true";

            var data = Encoding.ASCII.GetBytes(payload);
            
            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
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
                if(user.DisplayName != null && user.DisplayName != "")
                    item.SubItems.Add(user.DisplayName);
                else
                    item.SubItems.Add(user.Name);
                item.SubItems.Add(user.FirstName);
                item.SubItems.Add(user.LastName);
                item.SubItems.Add(user.Email);
                if (!string.IsNullOrEmpty(user.Phone1))
                    item.SubItems.Add(user.Phone1 + " (" + user.Phone1Type + ")");
                else
                    item.SubItems.Add("");
                item.Tag = user;
                if (user.IsCharity)
                    item.BackColor = Color.LightGreen;
                LstPeople.Items.Add(item);
            }
            LstPeople.ListViewItemSorter = new ListViewItemComparer(SortColumn, SortAscend);
            LstPeople.Sort();
            LstPeople.EndUpdate();
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
            var dialog = new FrmArtistDetails((Person)LstPeople.SelectedItems[0].Tag);
            dialog.ShowDialog();
        }

        private void BtnClear_Click(object sender, EventArgs e)
        {
            TxtEmail.Text = "";
            TxtDisplayName.Text = "";
            TxtLastName.Text = "";
        }

        private void BtnNewAccount_Click(object sender, EventArgs e)
        {
            var dialog = new FrmNewPerson();
            if (dialog.ShowDialog() == DialogResult.OK)
            {
                var person = dialog.Person;
                person.Save();

                var data = Encoding.ASCII.GetBytes("action=ModifyPermission&id=" + person.PeopleID + "&modification=Add");
                var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
                request.ContentLength = data.Length;
                request.ContentType = "application/x-www-form-urlencoded";
                request.Method = "POST";
                using (var stream = request.GetRequestStream())
                    stream.Write(data, 0, data.Length);

                var response = (HttpWebResponse)request.GetResponse();
                var results = new StreamReader(response.GetResponseStream()).ReadToEnd();

                LstPeople.SelectedItems.Clear();
                var item = new ListViewItem { Text = person.PeopleID.ToString() };
                item.SubItems.Add(person.FirstName);
                item.SubItems.Add(person.LastName);
                item.SubItems.Add(person.Email);
                if (!string.IsNullOrEmpty(person.Phone1))
                    item.SubItems.Add(person.Phone1 + " (" + person.Phone1Type + ")");
                else
                    item.SubItems.Add("");
                item.Tag = person;
                item.Selected = true;
                LstPeople.Items.Add(item);
                LstPeople.ListViewItemSorter = new ListViewItemComparer(SortColumn, SortAscend);
                LstPeople.Sort();
                item.Focused = true;
            }
        }

        private void SearchField_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (e.KeyChar == 13)
            {
                BtnSearch_Click(sender, new EventArgs());
                e.Handled = true;
            }
        }

        private void ChkOnlyInventory_CheckedChanged(object sender, EventArgs e)
        {
            Program.WithInventoryOnly = ChkOnlyInventory.Checked;
        }
    }
}
