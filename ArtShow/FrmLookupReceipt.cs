using Newtonsoft.Json;
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Net;
using System.Text;
using System.Windows.Forms;

namespace ArtShow
{
    public partial class FrmLookupReceipt : Form
    {
        public FrmLookupReceipt()
        {
            InitializeComponent();
            var payload = "action=GetAllReceipts?year=" + Program.Year.ToString();
            var data = Encoding.ASCII.GetBytes(payload);

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var receipts = JsonConvert.DeserializeObject<List<ReceiptDetails>>(results);
        }
    }
}
