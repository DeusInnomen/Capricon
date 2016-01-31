using System;
using System.Collections.Generic;
using System.Drawing;
using System.IO;
using System.Net;
using System.Text;
using System.Windows.Forms;
using CryptSharp;
using Newtonsoft.Json;

namespace Registration
{
    public partial class FrmAccountInfo : Form
    {
        private Person CurrentPerson { get; set; }

        public FrmAccountInfo(Person person = null)
        {
            InitializeComponent();
            CmbState.Items.Add(new USState("", "N/A"));
            CmbState.Items.AddRange(StateArray.States());
            CmbState.SelectedIndex = CmbState.FindString("IL");
            CmbPhoneType1.SelectedItem = "";
            CmbPhoneType2.SelectedItem = "";

            CurrentPerson = person;

            if (person == null) return;

            TxtFirstName.Text = person.FirstName;
            TxtLastName.Text = person.LastName;
            TxtAddress1.Text = person.Address1;
            TxtAddress2.Text = person.Address2;
            TxtCity.Text = person.City;
            CmbState.SelectedIndex = person.State != null ? CmbState.FindString(person.State) : 0;
            TxtZip.Text = person.ZipCode;
            TxtCountry.Text = person.Country;
            TxtPhone1.Text = person.Phone1;
            if (person.Phone1Type != null) CmbPhoneType1.SelectedItem = person.Phone1Type;
            TxtPhone2.Text = person.Phone2;
            if (person.Phone2Type != null) CmbPhoneType2.SelectedItem = person.Phone2Type;
            TxtEmail.Text = person.Email;
            TxtBadgeName.Text = person.BadgeName;
            ChkBanned.Checked = person.Banned;
            TxtHeardFrom.Text = person.HeardFrom;
        }

        private void BtnClose_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.OK;
        }

        private void BtnSave_Click(object sender, EventArgs e)
        {
            LblMessage.Text = "";
            Application.DoEvents();

            var target = ValidateFields();
            if (target != null)
            {
                LblMessage.Text = "Required fields not filled in.";
                target.Focus();
                target.BackColor = Color.Yellow;
                return;
            }

            var id = (CurrentPerson != null ? CurrentPerson.PeopleID : null);
            CurrentPerson = new Person()
                {
                    PeopleID = id,
                    FirstName = TxtFirstName.Text,
                    LastName = TxtLastName.Text,
                    Address1 = TxtAddress1.Text,
                    Address2 = TxtAddress2.Text,
                    City = TxtCity.Text,
                    State = ((USState)CmbState.SelectedItem).Abbreviation,
                    ZipCode = TxtZip.Text,
                    Country = TxtCountry.Text,
                    Phone1 = TxtPhone1.Text,
                    Phone1Type = TxtPhone1.TextLength > 0 ? (string)CmbPhoneType1.SelectedItem : "",
                    Phone2 = TxtPhone2.Text,
                    Phone2Type = TxtPhone2.TextLength > 0 ? (string)CmbPhoneType2.SelectedItem : "",
                    Email = TxtEmail.Text,
                    BadgeName =
                        (TxtBadgeName.Text != "" ? TxtBadgeName.Text : TxtFirstName.Text + " " + TxtLastName.Text),
                    Banned = ChkBanned.Checked,
                    HeardFrom = TxtHeardFrom.Text
                };
            if (id == null)
                CurrentPerson.Save(TxtPassword1.Text);
            else
                CurrentPerson.Save();

            if (CurrentPerson.LastError != null)
                LblMessage.Text = "An error occurred trying to save the account: " + CurrentPerson.LastError;
            else
                LblMessage.Text = "Account saved successfully.";

        }

        private Control ValidateFields()
        {
            foreach (Control ctrl in Controls) ctrl.BackColor = SystemColors.Window;

            if (TxtFirstName.Text.Trim().Length == 0) return TxtFirstName;
            if (TxtLastName.Text.Trim().Length == 0) return TxtLastName;
            if (TxtAddress1.Text.Trim().Length == 0) return TxtAddress1;
            if (TxtCity.Text.Trim().Length == 0) return TxtCity;
            if ((TxtCountry.Text == "USA" || TxtCountry.Text == "") && (string)CmbState.SelectedValue == "") return CmbState;
            if (TxtEmail.Text.Trim().Length == 0) return TxtEmail;
            if (CurrentPerson == null)
            {
                 if (TxtPassword1.Text.Trim().Length == 0 || TxtPassword2.Text.Trim().Length == 0 || TxtPassword1.Text != TxtPassword2.Text)
                 {
                     TxtPassword1.Text = "";
                     TxtPassword2.Text = "";
                     return TxtPassword1;
                 }
            }
            return null;
        }
    }
}
