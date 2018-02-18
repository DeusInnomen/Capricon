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
        public StripeCharge Charge { get; private set; }
        public StripeException Error { get; private set; }
        public bool FirstTry { get; set; }

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
                var tokenService = new StripeTokenService();
                try
                {
                    var description = Description + " for ";
                    var tokenData = new StripeTokenCreateOptions
                    {
                        Card = new StripeCreditCardOptions
                        {
                            Number = CardNumber,
                            ExpirationMonth = Convert.ToInt32(CardMonth),
                            ExpirationYear = Convert.ToInt32(CardYear),
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

                    var chargeData = new StripeChargeCreateOptions
                    {
                        SourceTokenOrExistingSourceId = token.Id,
                        Description = description,
                        Amount = Convert.ToInt32(Amount * 100),
                        Currency = "usd"
                    };
                    var chargeService = new StripeChargeService();

                    if (!FirstTry)
                    {
                        // Double-check to see if we already have a charge recently that matches the details of this. Helps with dealing
                        // with timeout scenarios to prevent double-charges.
                        var lastCharges = chargeService.List(new StripeChargeListOptions() { Limit = 20 });
                        foreach (var charge in lastCharges)
                        {
                            if (charge.Source.Card.Last4 == CardNumber.Substring(CardNumber.Length - 3) &&
                                charge.Source.Card.ExpirationMonth == Convert.ToInt32(CardMonth) &&
                                charge.Source.Card.ExpirationYear == Convert.ToInt32(CardYear) &&
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
                Application.DoEvents();

            Cursor = Cursors.Default;
            DialogResult = DialogResult.OK;
        }
    }
}
