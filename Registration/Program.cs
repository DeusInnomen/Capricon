using System;
using System.Net;
using System.Collections.Generic;
using System.Linq;
using System.Windows.Forms;
using DYMO.Label.Framework;
using Stripe;

namespace Registration
{
    static class Program
    {
        /// <summary>
        /// The URL to access the web services at.
        /// </summary>
        public static string URL
        {
            get { return Properties.Settings.Default.ServicesURL; }
        }

        /// <summary>
        /// The main entry point for the application.
        /// </summary>
        [STAThread]
        static void Main()
        {
            System.Windows.Forms.Application.EnableVisualStyles();
            System.Windows.Forms.Application.SetCompatibleTextRenderingDefault(false);
            ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;
            StripeConfiguration.ApiKey = Properties.Settings.Default.StripeKey;
            StripeConfiguration.MaxNetworkRetries = 3;

#if !DEBUG
{  
  
            var login = new FrmLogin();
            var result = login.ShowDialog();
            if(result == DialogResult.Cancel)
                System.Windows.Forms.Application.Exit();
            else
                System.Windows.Forms.Application.Run(new FrmMain());
}
#else
            System.Windows.Forms.Application.Run(new FrmMain());
#endif
        }
    }
}
