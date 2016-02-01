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

namespace Registration
{
    public partial class FrmProcessing : Form
    {
        public Person Person { get; set; }
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
            Charge = null;

            var thread = new Thread(new ThreadStart(delegate
            {
                var api = Properties.Settings.Default.StripeKey;
                var tokenService = new StripeTokenService(api);
                try
                {
                    var tokenData = new StripeTokenCreateOptions
                    {
                        CardAddressLine1 = Person.Address1,
                        CardAddressLine2 = Person.Address2,
                        CardAddressCity = Person.City,
                        CardAddressState = Person.State,
                        CardAddressZip = Person.ZipCode,
                        CardAddressCountry = Person.Country,
                        CardName = Person.Name,
                        CardNumber = CardNumber,
                        CardExpirationMonth = CardMonth,
                        CardExpirationYear = CardYear,
                        CardCvc = CardCVC
                    };
                    var token = tokenService.Create(tokenData);

                    var chargeData = new StripeChargeCreateOptions()
                    {
                        TokenId = token.Id,
                        Description = "Purchases for " + Person.Name + " (#" + Person.PeopleID + ")",
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
