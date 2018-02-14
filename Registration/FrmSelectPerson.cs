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
    public partial class FrmSelectPerson : Form
    {
        public Person Person { get; private set; }
        private Person NewPerson { get; set; }
        private List<Person> People { get; set; }

        private int SortColumn = 0;
        private bool SortAscend = true;
        private bool useNewPerson = false;


        public FrmSelectPerson()
        {
            InitializeComponent();
            People = null;
            TxtLastName.Focus();
        }

        private void BtnSelect_Click(object sender, EventArgs e)
        {
            Person = useNewPerson ? NewPerson : (Person)LstPeople.SelectedItems[0].Tag;
            DialogResult = DialogResult.OK;
        }

        private void LstPeople_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (LstPeople.SelectedItems.Count > 0)
            {
                TxtRecipientName.Text = ((Person)LstPeople.SelectedItems[0].Tag).Name;
                BtnSelect.Enabled = true;
                BtnAddChild.Enabled = ((Person)LstPeople.SelectedItems[0].Tag).ParentID == null;
                useNewPerson = false;
            }
            else if (NewPerson != null)
            {
                TxtRecipientName.Text = NewPerson.Name;
                BtnSelect.Enabled = true;
                BtnAddChild.Enabled = true;
                useNewPerson = true;
            }
            else
            {
                TxtRecipientName.Text = "";
                BtnSelect.Enabled = false;
                BtnAddChild.Enabled = false;
                useNewPerson = false;
            }

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
            if (LstPeople.SelectedItems.Count > 0)
                BtnSelect_Click(sender, e);

        }

        private void TxtLastName_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (e.KeyChar == 13)
            {
                BtnSearch_Click(this, new EventArgs());
                e.Handled = true;
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
                item.SubItems.Add(person.ParentName ?? "");
                item.Tag = person;
                LstPeople.Items.Add(item);
            }
            LstPeople.ListViewItemSorter = new ListViewItemComparer(SortColumn, SortAscend);
            LstPeople.Sort();
            LstPeople.EndUpdate();
            Cursor = Cursors.Default;
        }

        private void BtnAddPerson_Click(object sender, EventArgs e)
        {
            var addForm = new FrmNewPerson(NewPerson);
            if (addForm.ShowDialog(this) == DialogResult.OK)
            {
                NewPerson = addForm.Person;
                TxtRecipientName.Text = NewPerson.Name;
                BtnSelect.Enabled = true;
                BtnAddChild.Enabled = true;
                useNewPerson = true;
            }
            DialogResult = DialogResult.None;
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }

        private void BtnAddChild_Click(object sender, EventArgs e)
        {
            if (useNewPerson && NewPerson != null)
            {
                NewPerson.Save();
                LstPeople.SelectedItems[0].Tag = NewPerson;
            }

            var addForm = new FrmNewRelatedPerson(null, useNewPerson ? NewPerson : (Person)LstPeople.SelectedItems[0].Tag);
            if (addForm.ShowDialog(this) == DialogResult.OK)
            {
                NewPerson = addForm.Person;
                TxtRecipientName.Text = NewPerson.Name;
                BtnSelect.Enabled = true;
                useNewPerson = true;
            }
            DialogResult = DialogResult.None;

        }
    }
}
