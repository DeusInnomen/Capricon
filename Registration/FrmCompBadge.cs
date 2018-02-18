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
    public partial class FrmCompBadge : Form
    {
        public Person Person { get; private set; }
        private List<Person> People { get; set; }

        private int SortColumn = 0;
        private bool SortAscend = true;
        private bool useNewPerson = false;

        public FrmCompBadge()
        {
            InitializeComponent();
            People = null;
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }

        private void LstPeople_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (LstPeople.SelectedItems.Count > 0)
            {
                TxtRecipientName.Text = ((Person)LstPeople.SelectedItems[0].Tag).Name;
                useNewPerson = false;
            }
            else if (Person != null)
            {
                TxtRecipientName.Text = Person.Name;
                useNewPerson = true;
            }
            else
            {
                TxtRecipientName.Text = "";
                useNewPerson = false;
            }
            BtnIssue.Enabled = (TxtDepartment.TextLength > 0) && ((LstPeople.SelectedItems.Count > 0) || People != null);
        }

        private void BtnAddPerson_Click(object sender, EventArgs e)
        {
            var addForm = new FrmNewPerson(Person);
            if (addForm.ShowDialog() == DialogResult.OK)
            {
                Person = addForm.Person;
                TxtRecipientName.Text = Person.Name;
                TxtBadgeName.Text = Person.BadgeName;
                useNewPerson = true;
                BtnIssue.Enabled = (TxtDepartment.TextLength > 0) && ((LstPeople.SelectedItems.Count > 0) || People != null);
            }

        }

        private void BtnSearch_Click(object sender, EventArgs e)
        {
            Cursor = Cursors.WaitCursor;
            if (People == null)
            {
                var data = Encoding.ASCII.GetBytes("action=GetUsers");
                var request = WebRequest.Create(Program.URL + "/functions/userQuery.php");
                request.ContentLength = data.Length;
                request.ContentType = "application/x-www-form-urlencoded";
                request.Method = "POST";
                request.Timeout = 20000;
                using (var stream = request.GetRequestStream())
                    stream.Write(data, 0, data.Length);

                var response = (HttpWebResponse)request.GetResponse();
                var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
                People = JsonConvert.DeserializeObject<List<Person>>(results);
            }

            LstPeople.BeginUpdate();
            LstPeople.Items.Clear();
            var toAdd = TxtLastName.Text.Length > 0 ?
                People.FindAll(b => b.LastName.ToLower().StartsWith(TxtLastName.Text.ToLower())).ToList() :
                People;
            foreach (var person in toAdd)
            {
                var item = new ListViewItem
                {
                    Text = person.FirstName
                };
                item.SubItems.Add(person.LastName);
                item.SubItems.Add(person.Email);
                item.SubItems.Add(person.BadgeName);
                item.Tag = person;
                LstPeople.Items.Add(item);
            }
            LstPeople.ListViewItemSorter = new ListViewItemComparer(SortColumn, SortAscend);
            LstPeople.Sort();
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

        private void BtnIssue_Click(object sender, EventArgs e)
        {
            Cursor = Cursors.WaitCursor;
            var payload = "action=CompBadge&department=" + TxtDepartment.Text;
            if (useNewPerson)
                Person.Save();
            var targetPerson = useNewPerson ? Person : (Person)LstPeople.SelectedItems[0].Tag;
            payload += "&peopleID=" + targetPerson.PeopleID;
            payload += "&badgeName=" + HttpUtility.UrlEncode(TxtBadgeName.Text);

            var data = Encoding.ASCII.GetBytes(payload);

            var request = WebRequest.Create(Program.URL + "/functions/userQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            request.Timeout = 20000;
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var webResponse = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(webResponse.GetResponseStream()).ReadToEnd();
            var response = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);
            if ((string)response["result"] == "Success")
            {
                MessageBox.Show("Badge successfully created for " + TxtRecipientName.Text + ".", "Success",
                                MessageBoxButtons.OK, MessageBoxIcon.Information);
                DialogResult = DialogResult.OK;
            }
            else
            {
                MessageBox.Show("Failed to create record for new person: " + (string)response["message"], "Error",
                                MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
            Cursor = Cursors.Default;
        }

        private void TxtDepartment_TextChanged(object sender, EventArgs e)
        {
            BtnIssue.Enabled = (TxtDepartment.TextLength > 0) && ((LstPeople.SelectedItems.Count > 0) || People != null);
        }
    }
}
