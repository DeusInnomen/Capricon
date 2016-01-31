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
    public partial class FrmArtistDetails : Form
    {
        private Artist Artist { get; set; }
        private ArtistPresence Presence { get; set; }

        public FrmArtistDetails(Person person)
        {
            InitializeComponent();

            var data = Encoding.ASCII.GetBytes("action=GetArtist&PeopleID=" + person.PeopleID + "&Year=" + Program.Year.ToString());

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var artistDetails = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);

            if (artistDetails["details"] != null)
            {
                Artist = JsonConvert.DeserializeObject<Artist>(artistDetails["details"].ToString());
                TxtDisplayName.Text = Artist.DisplayName;
                TxtLegalName.Text = Artist.LegalName;
                ChkIsPro.Checked = Artist.IsPro;
                ChkIsEAP.Checked = Artist.IsEAP;
                TxtWebsite.Text = Artist.Website;
                TxtArtType.Text = Artist.ArtType;
                TxtArtistNotes.Text = Artist.Notes;
            }
            else
                Artist = new Artist
                    {
                        PeopleID = (int)person.PeopleID
                    };

            if (artistDetails["presence"] != null)
            {
                Presence = JsonConvert.DeserializeObject<ArtistPresence>(artistDetails["presence"].ToString());
                if (Presence.ArtistNumber != null) Artist.ArtistNumber = (int)Presence.ArtistNumber;
                TxtArtistNum.Text = Presence.ArtistNumber.ToString();
                ChkIsAttending.Checked = Presence.IsAttending;
                ChkHasBadge.Checked = Presence.HasBadge;
                ChkPrintShop.Checked = Presence.HasPrintShop;
                TxtAgentName.Text = Presence.AgentName;
                TxtAgentContact.Text = Presence.AgentContact;
                TxtShippingPref.Text = Presence.ShippingPref;
                TxtShippingAddress.Text = Presence.ShippingAddress;
                ChkElectricity.Checked = Presence.NeedsElectricity;
                NumTables.Value = Presence.NumTables;
                NumGrid.Value = Presence.NumGrid;
                TxtExhibitNotes.Text = Presence.Notes;
                CmbStatus.SelectedItem = Presence.Status;
                TxtStatusReason.Text = Presence.StatusReason;
                TxtLocationCode.Text = Presence.LocationCode;
                TxtShippingCost.Text = Presence.ShippingCost.ToString();
                TxtShippingPrepaid.Text = Presence.ShippingPrepaid.ToString();
                BtnInventory.Enabled = true;
            }
            else
            {
                Presence = new ArtistPresence();
                CmbStatus.SelectedItem = "Pending";
                BtnInventory.Enabled = false;
            }

        }

        private void BtnClose_Click(object sender, EventArgs e)
        {
            Close();
        }

        private void BtnInventory_Click(object sender, EventArgs e)
        {
            var inventory = new FrmArtistInventory(Artist, Presence);
            inventory.ShowDialog();
        }

        private void BtnSave_Click(object sender, EventArgs e)
        {
            Artist.DisplayName = TxtDisplayName.Text;
            Artist.LegalName = TxtLegalName.Text;
            Artist.IsPro = ChkIsPro.Checked;
            Artist.IsEAP = ChkIsEAP.Checked;
            Artist.Website = TxtWebsite.Text;
            Artist.ArtType = TxtArtType.Text;
            Artist.Notes = TxtArtistNotes.Text;

            Presence.IsAttending = ChkIsAttending.Checked;
            Presence.AgentName = TxtAgentName.Text;
            Presence.AgentContact = TxtAgentContact.Text;
            Presence.ShippingPref = TxtShippingPref.Text;
            Presence.ShippingAddress = TxtShippingAddress.Text;
            Presence.NeedsElectricity = ChkElectricity.Checked;
            Presence.NumTables = Convert.ToInt32(NumTables.Value);
            Presence.NumGrid = Convert.ToInt32(NumGrid.Value);
            Presence.HasPrintShop = ChkPrintShop.Checked;
            Presence.Notes = TxtExhibitNotes.Text;
            Presence.Status = CmbStatus.SelectedItem.ToString();
            Presence.StatusReason = TxtStatusReason.Text;
            Presence.LocationCode = TxtLocationCode.Text;
            Presence.ShippingCost = TxtShippingCost.TextLength > 0 ? Convert.ToDecimal(TxtShippingCost.Text) : (decimal?) null;
            Presence.ShippingPrepaid = TxtShippingPrepaid.TextLength > 0 ? Convert.ToDecimal(TxtShippingPrepaid.Text) : (decimal?)null;

            var payload = "action=SaveArtist&Year=" + Program.Year.ToString() + 
                "&artist=" + HttpUtility.UrlEncode(JsonConvert.SerializeObject(Artist)) +
                "&presence=" + HttpUtility.UrlEncode(JsonConvert.SerializeObject(Presence));
            var data = Encoding.ASCII.GetBytes(payload);

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var webResponse = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(webResponse.GetResponseStream()).ReadToEnd();
            var response = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);
            if (response.ContainsKey("error"))
            {
                LblMessage.Text = "An error occurred trying to save the artist: " + (string)response["error"];
                return;
            }
            if (response.ContainsKey("artistID"))
                Artist.ArtistID = Convert.ToInt32(response["artistID"]);
            if (response.ContainsKey("artistNumber"))
            {
                Artist.ArtistNumber = Convert.ToInt32(response["artistNumber"]);
                TxtArtistNum.Text = Artist.ArtistNumber.ToString();
            }
            if (response.ContainsKey("artistAttendingID"))
                Presence.ArtistAttendingID = Convert.ToInt32(response["artistAttendingID"]);

            LblMessage.Text = "Artist saved successfully.";
            BtnInventory.Enabled = true;
        }
    }
}
