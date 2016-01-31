using System;
using System.Collections.Generic;
using System.Linq;
using System.Windows.Forms;
using DYMO.Label.Framework;

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
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
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
