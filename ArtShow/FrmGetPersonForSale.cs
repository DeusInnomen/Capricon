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
    public partial class FrmGetPersonForSale : Form
    {
        public Person Person { get; private set; }
        private List<Person> People { get; set; }

        private int SortColumn = 0;
        private bool SortAscend = true;
        private bool useNewPerson = false;

        public FrmGetPersonForSale()
        {
            InitializeComponent();
            People = null;
            TxtLastName.Focus();
        }

        private void LstPeople_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (LstPeople.SelectedItems.Count > 0)
            {
                TxtRecipientName.Text = ((Person)LstPeople.SelectedItems[0].Tag).Name;
                BtnContinue.Enabled = true;
                useNewPerson = false;
            }
            else if (Person != null)
            {
                TxtRecipientName.Text = Person.Name;
                BtnContinue.Enabled = true;
                useNewPerson = true;
            }
            else
            {
                TxtRecipientName.Text = "";
                BtnContinue.Enabled = false;
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

        private void BtnSearch_Click(object sender, EventArgs e)
        {
            if (People == null)
            {
                var data = Encoding.ASCII.GetBytes("action=GetUsers");
                var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
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
            var badgeNumber = 0;
            int.TryParse(TxtLastName.Text.Trim(), out badgeNumber);

            var toAdd = badgeNumber != 0 ?
                People.FindAll(b => b.BadgeNumber == badgeNumber).ToList() :
                (TxtLastName.Text.Length > 0 ?
                People.FindAll(b => b.LastName.ToLower().StartsWith(TxtLastName.Text.ToLower())).ToList() :
                People);
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
        }

        private void BtnAddPerson_Click(object sender, EventArgs e)
        {
            var addForm = new FrmNewPerson(Person);
            if (addForm.ShowDialog() == DialogResult.OK)
            {
                Person = addForm.Person;
                TxtRecipientName.Text = Person.Name;
                BtnContinue.Enabled = true;
                useNewPerson = true;
            }
        }

        private void BtnContinue_Click(object sender, EventArgs e)
        {
            if (useNewPerson)
                Person.Save();
            var sales = useNewPerson ?
                new FrmSellItems(Person) :
                new FrmSellItems((Person)LstPeople.SelectedItems[0].Tag);
            sales.ShowDialog();

            LstPeople.SelectedItems.Clear();
            TxtLastName.Text = "";
            TxtRecipientName.Text = "";
            Person = null;
        }

        private void TxtLastName_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (e.KeyChar == 13)
            {
                BtnSearch_Click(this, new EventArgs());
                e.Handled = true;
            }
        }

        private void LstPeople_DoubleClick(object sender, EventArgs e)
        {
            if(LstPeople.SelectedItems.Count > 0)
                BtnContinue_Click(sender, e);
        }
    }
}
