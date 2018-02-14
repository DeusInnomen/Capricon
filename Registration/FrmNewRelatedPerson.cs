using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace Registration
{
    public partial class FrmNewRelatedPerson : Form
    {
        public Person Person { get; private set; }
        private Person ThisParent { get; set; }

        public FrmNewRelatedPerson(Person person = null, Person parent = null)
        {
            InitializeComponent();
            ThisParent = parent;

            Person = person;
            if (person == null) return;

            TxtFirstName.Text = person.FirstName;
            TxtLastName.Text = person.LastName;
            TxtPhone1.Text = person.Phone1;
            if (person.Phone1Type != null) CmbPhoneType1.SelectedItem = person.Phone1Type;
            TxtPhone2.Text = person.Phone2;
            if (person.Phone2Type != null) CmbPhoneType2.SelectedItem = person.Phone2Type;
            TxtBadgeName.Text = person.BadgeName;
        }

        private void BtnSave_Click(object sender, EventArgs e)
        {
            var target = ValidateFields();
            if (target != null)
            {
                target.Focus();
                target.BackColor = Color.Yellow;
                return;
            }

            Person = new Person()
            {
                FirstName = TxtFirstName.Text,
                LastName = TxtLastName.Text,
                Phone1 = TxtPhone1.Text,
                Phone1Type = TxtPhone1.TextLength > 0 ? (string)CmbPhoneType1.SelectedItem : "",
                Phone2 = TxtPhone2.Text,
                Phone2Type = TxtPhone2.TextLength > 0 ? (string)CmbPhoneType2.SelectedItem : "",
                BadgeName = (TxtBadgeName.Text != "" ? TxtBadgeName.Text : TxtFirstName.Text + " " + TxtLastName.Text),
                Banned = false,
                ParentID = ThisParent != null ? ThisParent.PeopleID : Person.ParentID,
                ParentName = ThisParent != null ? ThisParent.Name : Person.ParentName,
                ParentContact = ThisParent != null ? ThisParent.Phone1 : Person.ParentContact
            };

            DialogResult = DialogResult.OK;
        }

        private Control ValidateFields()
        {
            foreach (Control ctrl in Controls) ctrl.BackColor = SystemColors.Window;

            if (TxtFirstName.Text.Trim().Length == 0) return TxtFirstName;
            if (TxtLastName.Text.Trim().Length == 0) return TxtLastName;
            return null;
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }
    }
}
