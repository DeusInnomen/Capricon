using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading;
using System.Windows.Forms;
using Stripe;

namespace ArtShow
{
    public partial class FrmProcessing : Form
    {
        public string Description { get; set; }
        public Person Person { get; set; }
        public string PayeeName { get; set; }
        public string CardNumber { get; set; }
        public string CardMonth { get; set; }
        public string CardYear { get; set; }
        public string CardCVC { get; set; }
        public decimal Amount { get; set; }
        public Charge Charge { get; private set; }
        public StripeException Error { get; private set; }
        public bool FirstTry { get; set; }
        public string UniqueCode { get; set; }

        private bool _processingDone = false;

        public FrmProcessing()
        {
            InitializeComponent();
        }

        private void FrmProcessing_Shown(object sender, EventArgs e)
        {
            Cursor = Cursors.WaitCursor;
            Error = null;

            var thread = new Thread(new ThreadStart(delegate
            {
                var tokenService = new TokenService();
                try
                {
                    var description = Description + " for ";
                    var tokenData = new TokenCreateOptions
                    {
                        Card = new CreditCardOptions
                        {
                            Number = CardNumber,
                            ExpMonth = Convert.ToInt32(CardMonth),
                            ExpYear = Convert.ToInt32(CardYear),
                            Cvc = CardCVC
                        }
                    };

                    if (Person != null)
                    {
                        tokenData.Card.AddressLine1 = Person.Address1;
                        tokenData.Card.AddressLine2 = Person.Address2;
                        tokenData.Card.AddressCity = Person.City;
                        tokenData.Card.AddressState = Person.State;
                        tokenData.Card.AddressZip = Person.ZipCode;
                        tokenData.Card.AddressCountry = Person.Country;
                        tokenData.Card.Name = Person.Name;
                        description += Person.Name + " (#" + Person.PeopleID + ")";
                    }
                    else
                    {
                        tokenData.Card.Name = PayeeName;
                        description += PayeeName;
                    }

                    var token = tokenService.Create(tokenData);

                    var chargeData = new ChargeCreateOptions
                    {
                        SourceId = token.Id,
                        Description = description,
                        Amount = Convert.ToInt32(Amount * 100),
                        Currency = "usd"
                    };
                    chargeData.Metadata = new Dictionary<string, string> { { "Code", UniqueCode } };

                    var chargeService = new ChargeService();

                    if (!FirstTry)
                    {
                        // Double-check to see if we already have a charge recently that matches the details of this. Helps with dealing
                        // with timeout scenarios to prevent double-charges.
                        var lastCharges = chargeService.List(new ChargeListOptions() { Limit = 20 });
                        foreach (var charge in lastCharges)
                        {
                            if (charge.Metadata.ContainsKey("Code") && charge.Metadata["Code"] == UniqueCode)
                            {
                                Charge = charge;
                                break;
                            }
                            if (((Card)charge.Source).Last4 == CardNumber.Substring(CardNumber.Length - 3) &&
                                charge.Amount == Convert.ToInt32(Amount * 100) &&
                                charge.Description == description)
                            {
                                Charge = charge;
                                break;
                            }
                        }
                    }

                    if (Charge == null)
                        Charge = chargeService.Create(chargeData);
                }
                catch (StripeException ex)
                {
                    Error = ex;
                }
                _processingDone = true;
            }));
            thread.Start();

            while (!_processingDone)
                System.Windows.Forms.Application.DoEvents();

            Cursor = Cursors.Default;
            DialogResult = DialogResult.OK;
        }
    }
}
