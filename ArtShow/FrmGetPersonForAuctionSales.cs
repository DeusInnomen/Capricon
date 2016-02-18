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
    public partial class FrmGetPersonForAuctionSales : Form
    {
        private int SortColumn = 2;
        private bool SortAscend = true;

        public FrmGetPersonForAuctionSales()
        {
            InitializeComponent();
            LstPeople.Columns[2].ImageIndex = 0;
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
            BtnSettle_Click(sender, e);
        }

        private void BtnSearch_Click(object sender, EventArgs e)
        {
            var payload = "action=GetPeopleForPickup&year=" + Program.Year.ToString();
            if (TxtID.TextLength > 0)
                payload += "&id=" + TxtID.Text;
            else if (TxtLastName.TextLength > 0)
                payload += "&lastName=" + HttpUtility.UrlEncode(TxtLastName.Text);

            var data = Encoding.ASCII.GetBytes(payload);

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var people = JsonConvert.DeserializeObject<List<PersonPickup>>(results);

            LstPeople.BeginUpdate();
            LstPeople.Items.Clear();
            foreach (var person in people)
            {
                var item = new ListViewItem { Text = person.BadgeNumber.ToString() };
                item.SubItems.Add(person.FirstName);
                item.SubItems.Add(person.LastName);
                item.SubItems.Add(person.TotalPieces.ToString());
                item.SubItems.Add(person.TotalDue.ToString("C"));
                item.Tag = person;
                LstPeople.Items.Add(item);
            }
            LstPeople.ListViewItemSorter = new ListViewItemComparer(SortColumn, SortAscend);
            LstPeople.Sort();
            LstPeople.EndUpdate();
        }

        private void BtnClear_Click(object sender, EventArgs e)
        {
            TxtID.Text = "";
            TxtLastName.Text = "";
        }

        private void BtnSettle_Click(object sender, EventArgs e)
        {
            if (LstPeople.SelectedItems.Count == 0 && LstPeople.Items.Count == 1)
                LstPeople.Items[0].Selected = true;
            if (LstPeople.SelectedItems.Count == 0) return;
            var person = (PersonPickup) LstPeople.SelectedItems[0].Tag;
            var dialog = new FrmSellAuctionItemsToPerson(person);
            dialog.ShowDialog();
            dialog.Close();
        }
    }
}
