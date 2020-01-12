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
                        tokenData.Card = new CreditCardOptions
                        {
                            AddressLine1 = Person.Address1,
                            AddressLine2 = Person.Address2,
                            AddressCity = Person.City,
                            AddressState = Person.State,
                            AddressZip = Person.ZipCode,
                            AddressCountry = Person.Country,
                            Name = Person.Name
                        };
                        description += Person.Name + " (#" + Person.PeopleID + ")";
                    }
                    else
                    {
                        tokenData.Card = new CreditCardOptions { Name = PayeeName };
                        description += PayeeName;
                    }

                    var token = tokenService.Create(tokenData);

                    var chargeData = new ChargeCreateOptions
                    {
                        Source = token.Id,
                        Description = description,
                        Amount = Convert.ToInt32(Amount * 100),
                        Currency = "usd"
                    };

                    var chargeService = new ChargeService();
                    Charge = chargeService.Create(chargeData, new RequestOptions { IdempotencyKey = UniqueCode });
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
