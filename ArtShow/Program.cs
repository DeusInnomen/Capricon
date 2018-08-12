using System;
using System.Net;
using System.Collections.Generic;
using System.Linq;
using System.Windows.Forms;
using Stripe;

namespace ArtShow
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
        /// The year to load from the database.
        /// </summary>
        public static int Year { get; set; }

        /// <summary>
        /// The tax rate that should be charged for the sale of goods at the convention.
        /// </summary>
        public static decimal TaxRate
        {
            get { return Properties.Settings.Default.TaxRate / 100; }
        }

        /// <summary>
        /// Saves the current state of the "Show Artists With Inventory Only" checkbox on forms with Artist searching.
        /// </summary>
        public static bool WithInventoryOnly { get; set; }

        /// <summary>
        /// The main entry point for the application.
        /// </summary>
        [STAThread]
        static void Main()
        {
            Year = DateTime.Now.Month > 4 ? DateTime.Now.Year + 1 : DateTime.Now.Year;
            WithInventoryOnly = false;
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;
            StripeConfiguration.SetApiKey(Properties.Settings.Default.StripeKey);

#if !DEBUG
            {  
  
            var login = new FrmLogin();
            var result = login.ShowDialog();
            if(result == DialogResult.Cancel)
                Application.Exit();
            else
                Application.Run(new FrmMain());
}
#else
            Application.Run(new FrmMain());
#endif
        }
    }
}
