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
                var api = Properties.Settings.Default.StripeKey;
                var tokenService = new StripeTokenService(api);
                try
                {
                    var description = Description + " for ";
                    var tokenData = new StripeTokenCreateOptions
                    {
                        CardNumber = CardNumber,
                        CardExpirationMonth = CardMonth,
                        CardExpirationYear = CardYear,
                        CardCvc = CardCVC
                    };

                    if (Person != null)
                    {
                        tokenData.CardAddressLine1 = Person.Address1;
                        tokenData.CardAddressLine2 = Person.Address2;
                        tokenData.CardAddressCity = Person.City;
                        tokenData.CardAddressState = Person.State;
                        tokenData.CardAddressZip = Person.ZipCode;
                        tokenData.CardAddressCountry = Person.Country;
                        tokenData.CardName = Person.Name;
                        description += Person.Name + " (#" + Person.PeopleID + ")";
                    }
                    else
                    {
                        tokenData.CardName = PayeeName;
                        description += PayeeName;
                    }

                    var token = tokenService.Create(tokenData);

                    var chargeData = new StripeChargeCreateOptions
                    {
                        TokenId = token.Id,
                        Description = description,
                        AmountInCents = Convert.ToInt32(Amount * 100),
                        Currency = "usd"
                    };
                    var chargeService = new StripeChargeService(api);

                    if (!FirstTry)
                    {
                        // Double-check to see if we already have a charge recently that matches the details of this. Helps with dealing
                        // with timeout scenarios to prevent double-charges.
                        var lastCharges = chargeService.List(20, 0, null);
                        foreach (var charge in lastCharges)
                        {
                            if (charge.StripeCard.Last4 == CardNumber.Substring(CardNumber.Length - 4) &&
                                charge.StripeCard.ExpirationMonth.PadLeft(2, '0') == CardMonth &&
                                charge.StripeCard.ExpirationYear.Substring(2) == CardYear &&
                                charge.AmountInCents == Convert.ToInt32(Amount * 100) &&
                                charge.Description == "Purchases for " + Person.Name + " (#" + Person.PeopleID + ")")
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
