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
            Charge = null;

            var thread = new Thread(new ThreadStart(delegate
            {
                var tokenService = new TokenService();
                try
                {
                    var tokenData = new TokenCreateOptions
                    {
                        Card = new CreditCardOptions
                        {
                            AddressLine1 = Person.Address1,
                            AddressLine2 = Person.Address2,
                            AddressCity = Person.City,
                            AddressState = Person.State,
                            AddressZip = Person.ZipCode,
                            AddressCountry = Person.Country,
                            Name = Person.Name,
                            Number = CardNumber,
                            ExpMonth = Convert.ToInt32(CardMonth),
                            ExpYear = Convert.ToInt32(CardYear),
                            Cvc = CardCVC
                        }
                    };
                    
                    var token = tokenService.Create(tokenData);

                    var chargeData = new ChargeCreateOptions()
                    {                        
                        SourceId = token.Id,
                        Description = "Purchases for " + Person.Name + " (#" + Person.PeopleID + ")",
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
                            if(charge.Metadata.ContainsKey("Code") && charge.Metadata["Code"] == UniqueCode)
                            {
                                Charge = charge;
                                break;
                            }
                            if (((Card)charge.Source).Last4 == CardNumber.Substring(CardNumber.Length - 4) &&
                                charge.Amount == Convert.ToInt32(Amount * 100) &&
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
                System.Windows.Forms.Application.DoEvents();

            Cursor = Cursors.Default;
            DialogResult = DialogResult.OK;
        }
    }
}
