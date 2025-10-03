<?php
#region Includlar:

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set('memory_limit', '8192M');

$trace = isset($_GET['trace']) ? $_GET['trace'] : "0" ;

include '../../_conn.php';
include '../../_conn_fm.php';

#endregion

#region Fonksiyonlar

function SehirKodu($cityName) {
    $cities = array(
        "ADANA" => 1,
        "GIRESUN" => 28,
        "SAMSUN" => 55,
        "ADIYAMAN" => 2,
        "GÜMÜŞHANE" => 29,
        "SIIRT" => 56,
        "AFYONKARAHİSAR" => 3,
        "HAKKÂRİ" => 30,
        "SİNOP" => 57,
        "AĞRI" => 4,
        "HATAY" => 31,
        "SİVAS" => 58,
        "AMASYA" => 5,
        "ISPARTA" => 32,
        "TEKİRDAĞ" => 59,
        "ANKARA" => 6,
        "MERSİN" => 33,
        "TOKAT" => 60,
        "ANTALYA" => 7,
        "İSTANBUL" => 34,
        "TRABZON" => 61,
        "ARTVİN" => 8,
        "İZMİR" => 35,
        "TUNCELİ" => 62,
        "AYDIN" => 9,
        "KARS" => 36,
        "ŞANLIURFA" => 63,
        "BALIKESİR" => 10,
        "KASTAMONU" => 37,
        "UŞAK" => 64,
        "BİLECIK" => 11,
        "KAYSERİ" => 38,
        "VAN" => 65,
        "BİNGÖL" => 12,
        "KIRKLARELİ" => 39,
        "YOZGAT" => 66,
        "BİTLİS" => 13,
        "KIRŞEHİR" => 40,
        "ZONGULDAK" => 67,
        "BOLU" => 14,
        "KOCAELİ" => 41,
        "AKSARAY" => 68,
        "BURDUR" => 15,
        "KONYA" => 42,
        "BAYBURT" => 69,
        "BURSA" => 16,
        "KÜTAHYA" => 43,
        "KARAMAN" => 70,
        "ÇANAKKALE" => 17,
        "MALATYA" => 44,
        "KIRIKKALE" => 71,
        "ÇANKIRI" => 18,
        "MANİSA" => 45,
        "BATMAN" => 72,
        "ÇORUM" => 19,
        "KAHRAMANMARAŞ" => 46,
        "ŞIRNAK" => 73,
        "DENİZLİ" => 20,
        "MARDİN" => 47,
        "BARTIN" => 74,
        "DİYARBAKIR" => 21,
        "MUĞLA" => 48,
        "ARDAHAN" => 75,
        "EDİRNE" => 22,
        "MUŞ" => 49,
        "IĞDIR" => 76,
        "ELAZIĞ" => 23,
        "NEVŞEHİR" => 50,
        "YALOVA" => 77,
        "ERZİNCAN" => 24,
        "NİĞDE" => 51,
        "KARABÜK" => 78,
        "ERZURUM" => 25,
        "ORDU" => 52,
        "KİLİS" => 79,
        "ESKİŞEHİR" => 26,
        "RİZE" => 53,
        "OSMANİYE" => 80,
        "GAZİANTEP" => 27,
        "SAKARYA" => 54,
        "DÜZCE" => 81
    );

    $cityName = ucwords(mb_strtoupper($cityName));

    if (isset($cities[$cityName])) {
        $code = $cities[$cityName];
        $codeSnippet = $code;
        return $codeSnippet;
    } else {
        return 'Şehir bulunamadı!';
    }
}
function splitTextIntoXML($text) {
    if ($text!="") {
        $xmlTag = "\n" . '<Comments type="Information">';
        $xmlCloseTag = '</Comments>';
        $splitText = str_split($text, 50);
        $xmlTags = array_map(function ($part) use ($xmlTag, $xmlCloseTag) {
            return $xmlTag . $part . $xmlCloseTag;
        }, $splitText);
        return implode('', $xmlTags);
    } else {
        return '';
    }
}
function XML_DATA_NE($kd,$note="")
{
    global $urunler;

    $head=<<<DATA
<OrderSupplier>

DATA;
    $SenderEnvelope=<<<DATA
<SenderEnvelope>
<SenderID>###SenderID###</SenderID>
<ReceiverID>###ReceiverID###</ReceiverID>
<DateStamp type="Message">###DateStampMessage###</DateStamp>
<VersionID>###VersionID###</VersionID>
<ControlNumber>###ControlNumber###</ControlNumber>
</SenderEnvelope>

DATA;
    $hea=<<<DATA
  <Header>
    <OrderType>###OrderType###</OrderType>
    <DocumentReference>
      <DocumentNumber type="PurchaseOrder">###DocumentNumber###</DocumentNumber>
      <DateStamp type="PurchaseOrder">###DateStamp###</DateStamp>
    </DocumentReference>
    <ReferenceNumber type="QuoteNumber">###ReferenceNumber###</ReferenceNumber>

DATA;
    $xml=<<<DATA
    <PartnerDescription type="Distributor">
      <PartnerNumber type="Supplier">###PartnerNumberSupplierDistributor###</PartnerNumber>
      <PartnerName1>###PartnerName1Distributor###</PartnerName1>
      <PartnerAddress>###PartnerAddressDistributor###</PartnerAddress>
      <PartnerPostalCode>###PartnerPostalCodeDistributor###</PartnerPostalCode>
      <PartnerCity>###PartnerCityDistributor###</PartnerCity>
      <PartnerCountryCode>###PartnerCountryCodeDistributor###</PartnerCountryCode>
      <ContactInformation>
        <ContactName>###ContactNameDistributor###</ContactName>
        <ContactPhone>###ContactPhoneDistributor###</ContactPhone>
        <ContactFax>###ContactFaxDistributor###</ContactFax>
        <ContactMail>###ContactMailDistributor###</ContactMail>
      </ContactInformation>
    </PartnerDescription>
    <PartnerDescription type="DeliveryParty">
      <PartnerNumber type="Distributor">###PartnerNumberSupplierDeliveryParty###</PartnerNumber>
      <PartnerNumber type="DUNS">###PartnerNumberDUNSDeliveryParty###</PartnerNumber>
      <PartnerName1>###PartnerName1DeliveryParty###</PartnerName1>
      <PartnerAddress>###PartnerAddressDeliveryParty###</PartnerAddress>
      <PartnerPostalCode>###PartnerPostalCodeDeliveryParty###</PartnerPostalCode>
      <PartnerCity>###PartnerCityDeliveryParty###</PartnerCity>
      <PartnerCountryCode>###PartnerCountryCodeDeliveryParty###</PartnerCountryCode>
      <ContactInformation>
        <ContactName>###ContactNameDeliveryParty###</ContactName>
        <ContactPhone>###ContactPhoneDeliveryParty###</ContactPhone>
        <ContactMail>###ContactMailDeliveryParty###</ContactMail>
      </ContactInformation>
    </PartnerDescription>
    <PartnerDescription type="Reseller">
      <PartnerNumber type="Supplier">###PartnerNumberSupplierReseller###</PartnerNumber>
      <PartnerName1>###PartnerName1Reseller###</PartnerName1>
      <PartnerAddress>###PartnerAddressReseller###</PartnerAddress>
      <PartnerPostalCode>###PartnerPostalCodeReseller###</PartnerPostalCode>
      <PartnerCity>###PartnerCityReseller###</PartnerCity>
      <PartnerCountryCode>###PartnerCountryCodeReseller###</PartnerCountryCode>
      <ContactInformation>
        <ContactName>###ContactNameReseller###</ContactName>
        <ContactPhone>###ContactPhoneReseller###</ContactPhone>
        <ContactFax>###ContactFaxReseller###</ContactFax>
        <ContactMail>###ContactMailReseller###</ContactMail>
      </ContactInformation>
    </PartnerDescription>
    <PartnerDescription type="EndUser">
      <PartnerNumber type="Supplier">###PartnerNumberSupplierEndUser###</PartnerNumber>
      <PartnerNumber type="Distributor">###PartnerNumberDistributorEndUser###</PartnerNumber>
      <PartnerName1>###PartnerName1EndUser###</PartnerName1>
      <PartnerAddress>###PartnerAddressEndUser###</PartnerAddress>
      <PartnerPostalCode>###PartnerPostalCodeEndUser###</PartnerPostalCode>
      <PartnerCity>###PartnerCityEndUser###</PartnerCity>
      <PartnerState>###PartnerStateEndUser###</PartnerState>
      <PartnerCountryCode>###PartnerCountryCodeEndUser###</PartnerCountryCode>
      <ContactInformation>
        <ContactName>###ContactNameEndUser###</ContactName>
        <ContactPhone>###ContactPhoneEndUser###</ContactPhone>
        <ContactMail>###ContactMailEndUser###</ContactMail>
      </ContactInformation>
    </PartnerDescription>@@@renewalek@@@@@@deal@@@
    <Currency>TRY</Currency>@@@comments@@@
  </Header>

DATA;
    $renewal_ek=<<<DATA
    <CompleteDelivery>###CompleteDelivery###</CompleteDelivery>
    <ShippingTerms>###ShippingTerms###</ShippingTerms>
DATA;
    $deal=<<<DATA

    <CompleteDelivery>###CompleteDelivery###</CompleteDelivery>
    <ShippingMethod>###ShippingMethod###</ShippingMethod>
    <ShippingAccount>###ShippingAccount###</ShippingAccount>
DATA;
    $serialnumber=<<<DATA

<SerialNumber>###SerialNumber###</SerialNumber>
DATA;
    $licenseid=<<<DATA

<LicenseID>###LicenseID###</LicenseID>
DATA;
    $line_ham=<<<DATA
<LineItem>
<LineNumber type="PurchaseOrder">###LineNumberPurchaseOrder###</LineNumber>
<ProductNumber type="Distributor">###ProductNumberDistributor###</ProductNumber>
<ProductNumber type="Supplier">###ProductNumberSupplier###</ProductNumber>@@@serialnumber@@@@@@licenseid@@@
<ProductDescription type="Distributor">###ProductDescriptionDistributor###</ProductDescription>
<Quantity type="Request">###QuantityRequest###</Quantity>
<MonetaryAmount type="NetCustomer">###MonetaryAmountNetCustomer###</MonetaryAmount>
<MonetaryAmount type="SumNetPosition">###MonetaryAmountSumNetPosition###</MonetaryAmount>
<DateStamp type="Request">###DateStampRequest###</DateStamp>
</LineItem>
DATA;
    $line=$line_ham;
    if ($kd=="4") {
        $line=str_replace("@@@licenseid@@@",$licenseid, $line);
        $xml=str_replace("@@@deal@@@", $deal, $xml);
    }
    if ($kd=="1") {
        $line=str_replace("@@@serialnumber@@@",$serialnumber, $line);
        $xml=str_replace("@@@renewalek@@@",$renewal_ek, $xml);
    }
    if ($kd=="2") {
        $line=str_replace("@@@licenseid@@@",$licenseid, $line);
        $xml=str_replace("@@@renewalek@@@",$renewal_ek, $xml);
    }
    if ($kd=="5") {
        $xml=str_replace("@@@deal@@@", $deal, $xml);
    }
    $com=splitTextIntoXML($note);
    if ($kd=="3") {
        $xml=str_replace("@@@comments@@@", $com, $xml);
    }

    $lines="";

    for ($i=0;$i<count($urunler);$i++) {
        $templine=$line_ham;
        //if ($kd=="4" || $kd=="2") {
        $templine=str_replace("@@@licenseid@@@",$licenseid, $templine);
        //}
        //if ($kd=="1") {
        $templine=str_replace("@@@serialnumber@@@",$serialnumber, $templine);
        //}
        $templine = str_replace("###LineNumberPurchaseOrder###", $i+1, $templine);
        $templine = str_replace("###ProductNumberDistributor###", $urunler[$i]["SKU"], $templine);
        $templine = str_replace("###ProductNumberSupplier###", $urunler[$i]["SKU"], $templine);
        $templine = str_replace("###ProductDescriptionDistributor###", $urunler[$i]["ACIKLAMA"], $templine);
        $templine = str_replace("###QuantityRequest###", $urunler[$i]["ADET"], $templine);
        $templine = str_replace("###MonetaryAmountNetCustomer###", $urunler[$i]["O_MALIYET"], $templine);
        $templine = str_replace("###MonetaryAmountSumNetPosition###", $urunler[$i]["ADET"]*$urunler[$i]["O_MALIYET"], $templine);
        $templine = str_replace("###DateStampRequest###", DATE("Ymd"), $templine);
        $templine = str_replace("###SerialNumber###", $urunler[$i]["LISANS"], $templine);
        $templine = str_replace("###LicenseID###", $urunler[$i]["MEVCUT_LISANS"], $templine);
        $lines .= $templine;
    }

    $lines=str_replace("@@@licenseid@@@","", $lines);
    $xml=str_replace("@@@deal@@@", "", $xml);
    $lines=str_replace("@@@serialnumber@@@","", $lines);
    $xml=str_replace("@@@renewalek@@@","", $xml);
    $xml=str_replace("@@@comments@@@", "", $xml);

    /*

<LineNumber type="PurchaseOrder">###LineNumberPurchaseOrder###</LineNumber>
<ProductNumber type="Distributor">###ProductNumberDistributor###</ProductNumber>
<ProductNumber type="Supplier">###ProductNumberSupplier###</ProductNumber>@@@serialnumber@@@@@@licenseid@@@
<ProductDescription type="Distributor">###ProductDescriptionDistributor###</ProductDescription>
<Quantity type="Request">###QuantityRequest###</Quantity>
<MonetaryAmount type="NetCustomer">###MonetaryAmountNetCustomer###</MonetaryAmount>
<MonetaryAmount type="SumNetPosition">###MonetaryAmountSumNetPosition###</MonetaryAmount>
<DateStamp type="Request">###DateStampRequest###</DateStamp>

     */
//    $lines="";
//    for ($i=0;$i<count($urunler);$i++) {
//        $tout=$line_ham;
//        $tout=str_replace("###LineNumberPurchaseOrder###",$i, $tout);
//        $tout=str_replace("###ProductNumberDistributor###",$urunler['SKU'], $tout);
//        $tout=str_replace("###ProductNumberSupplier###",$urunler['SKU'], $tout);
//        $tout=str_replace("###ProductDescriptionDistributor###",$urunler['ACIKLAMA'], $tout);
//        $tout=str_replace("###QuantityRequest###",$urunler['ADET'], $tout);
//        $tout=str_replace("###MonetaryAmountNetCustomer###",$urunler['O_MALIYET'], $tout);
//        $tout=str_replace("###MonetaryAmountSumNetPosition###",$urunler['B_MALIYET'], $tout);
//        $tout=str_replace("###DateStampRequest###",DATE("Ymd"), $tout);
//        $lines .= $tout;
//    }

    $out='<?xml version="1.0" encoding="utf-8"?>' . "\n" . $head . $SenderEnvelope . $hea . $xml . $lines ."\n</OrderSupplier>";
    return $out;
}

#endregion

#region Tablolar Okunuyor!

$siparis_no=$_GET['siparis_no'];

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set('memory_limit', '8192M');

$url = "select * from aa_erp_kt_siparisler where SIPARIS_NO=:siparis_no";
$stmt = $conn->prepare($url);
$stmt->execute(['siparis_no' => $siparis_no]);
$s = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$trace==3 ? print_r("######### \$s #########") : null ;
$trace == 3 ? print_r($s) : null ;

$url = "select m.adres madres,m.sehir msehir,m.posta_kodu mposta, f.* from aa_erp_kt_firsatlar f
LEFT JOIN aa_erp_kt_musteriler m
ON f.MUSTERI_ADI = m.musteri
where FIRSAT_NO=:firsat_no";
$stmt = $conn->prepare($url);
$stmt->execute(['firsat_no' => $s['X_FIRSAT_NO']]);
$f = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$trace==3 ? print_r("######### \$f #########") : null ;
$trace == 3 ? print_r($f) : null;

$url = "select *,(select kodu from aa_erp_kt_kampanyalar k where k.baslik like t.KAMPANYA) as kamkod
from aa_erp_kt_teklifler t where t.TEKLIF_NO=:teklif_no";
$stmt = $conn->prepare($url);
$stmt->execute(['teklif_no' => $s['X_TEKLIF_NO']]);
$t = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$trace == 3 ? print_r("######### \$t #########") : null ;
$trace == 3 ? print_r($t) : null;

$url = "select * from aaa_erp_kt_bayiler where CH_KODU=:ch_kodu";
$stmt = $conn->prepare($url);
$stmt->execute(['ch_kodu' => $f['BAYI_CHKODU']]);
$bayi = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$url = "select tu.*,su.LISANS from
aa_erp_kt_teklifler_urunler tu
LEFT JOIN aa_erp_kt_siparisler_urunler su ON tu.SKU = su.SKU AND tu.X_TEKLIF_NO = LEFT(X_SIPARIS_NO, LEN(X_SIPARIS_NO) - 2)
where tu.X_TEKLIF_NO = :teklif_no";
$stmt = $conn->prepare($url);
$stmt->execute(['teklif_no' => $t['TEKLIF_NO']]);
$tu = $stmt->fetchAll(PDO::FETCH_ASSOC);

$trace == 3 ? print_r("######### \$tu #########") : null ;
$trace == 3 ? print_r($tu) : null;

$url = "select EDI_NO from aa_erp_kt_sophos_edi_cari where CH_KODU =:chkodu";
$stmt = $conn->prepare($url);
$stmt->execute(['chkodu' => $f['BAYI_CHKODU']]);

$edi_no = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['EDI_NO'];

$trace==3 ? print_r("######### \$edi_no #########") : null ;
echo $trace == 3 ? "#############" . $edi_no . "#############" : null ;

$urunler=$tu;

#endregion

#region Degiskenler

// ---SenderEnveloper
$senderID = "KOMToIBM";
$receiverID = '5054074000010';
$dateStampMessage = Date("Ymd");
$versionID = "1.3";
$controlNumber = "";   // ? nedir Opsiyonel
//    SenderEnvelope ---

$orderType = "DS";
$documentNumber = "";
$dateStamp = Date("Ymd");  //Order Date

// • the Sophos Quote Reference
// • the Sophos Promotion code
// • the Sophos Deal registration number
$referencenumber="";

// ---PartnerDescription Distributor
$partnerNumberSupplierDistributor = "00856224";
$partnerName1Distributor = "Komtera Teknoloji A.S.";
$partnerAddressDistributor = "Kucukbakkalkoy Mah. Isiklar Cad.  No.17 Atasehir";
$partnerPostalCodeDistributor = "34758";
$partnerCityDistributor = "Istanbul";
$partnerCountryCodeDistributor = "TR";

$contactNameDistributor = "Ms. Ayse Aydin";
$contactPhoneDistributor = "+902164165151";
$contactFaxDistributor = "+902164165151";
$contactMailDistributor = "order@komtera.com";

// ---PartnerDescription DeliveryParty
$partnerNumberSupplierDeliveryParty = "";
$partnerNumberDUNSDeliveryParty = "";      // ?
$partnerName1DeliveryParty = "Komtera Teknoloji A.S.";
$partnerAddressDeliveryParty = "Kucukbakkalkoy Mah. Isiklar Cad.  No.17 Atasehir";
$partnerPostalCodeDeliveryParty = "34758";
$partnerCityDeliveryParty = "Istanbul";
$partnerCountryCodeDeliveryParty = "TR";
$contactNameDeliveryParty = "Ms. Ayse Aydin";
$contactPhoneDeliveryParty = "+902164165151";
$contactMailDeliveryParty = "order@komtera.com";

// ---PartnerDescription Reseller
$partnerNumberSupplierReseller = $edi_no;
$partnerName1Reseller = $f["BAYI_ADI"];
$partnerAddressReseller = $f["BAYI_ADRES"];
$partnerPostalCodeReseller = $bayi["POSTCODE"];
$partnerCityReseller = $f["BAYI_SEHIR"];
$partnerCountryCodeReseller = "TR";
$contactNameReseller = $f['BAYI_YETKILI_ISIM'];
$contactPhoneReseller = $f['BAYI_YETKILI_TEL'];
$contactFaxReseller = "";
$contactMailReseller = $f['BAYI_YETKILI_EPOSTA'];

// ---PartnerDescription EndUser
$partnerNumberSupplierEndUser = "";        // ?
$partnerNumberDistributorEndUser = "";     // ?
$partnerName1EndUser = $f['MUSTERI_ADI'];
$partnerAddressEndUser = $f['madres'];
$partnerPostalCodeEndUser = $f['mposta'];
$partnerCityEndUser = $f['msehir'];
$partnerStateEndUser = "";
$partnerCountryCodeEndUser = "TR";
$contactNameEndUser = $f['MUSTERI_YETKILI_ISIM'];
$contactPhoneEndUser = $f['MUSTERI_YETKILI_TEL'];
$contactMailEndUser = $f['MUSTERI_YETKILI_EPOSTA'];

$completeDelivery = "";
$shippingTerms = "";
$shippingMethod = "";
$shippingAccount = "";

#endregion

#region Tip ne o belirleniyor:

$note="uzun yazi olursa nereden kesecek bu yaziyi, bakalim ama daha devam ediyor!";

$url = "select * from TF_teklifler_attach where TEKLIF_NO='" . $t['TEKLIF_NO'] . "'";
$stmt2 = $conn2->prepare($url);
$stmt2->execute();
$ta = $stmt2->fetchAll(PDO::FETCH_ASSOC)[0];

$trace==3 ? print_r("######### \$ta #########") : null ;
$trace==3 ? print_r($ta) : null ;

$kd="";
if (strlen($ta['PDF_TEKLIF_ISMI'])>2) {
    $kd="4";
}
if ($kd=="" && $f['REGISTER']=="1") {
    $kd="5";
}
if ($kd=="" && strlen($t['KAMPANYA'])>2) {
    $kd="3";
}

$ilk=0;
$yen=0;
for ($uu=0;$uu<count($tu);$uu++) {
    if ($tu[$uu]['SATIS_TIPI']=="0") {
        $ilk++;
    }
    if ($tu[$uu]['SATIS_TIPI']=="1") {
        $yen++;
    }
}
if ($ilk>0 && $yen>0) {
    $kd="6";
}
if ($kd=="" && $ilk>0) {
    $kd="1";
}
if ($kd=="" && $yen>0) {
    $kd="2";
}

$KOTA_NO="";
// 1- Registered no var ise kota no o olmali! olmassa kotasyon dosyasi olmazsa kampanya
if ($t['kamkod']!="") {
    $KOTA_NO = $t['kamkod'];
} else if ($ta['KOTASYON_NUMARASI']!="") {
    $KOTA_NO = $ta['KOTASYON_NUMARASI'];
} else if ($f['REGISTER_DRNO']!="") {
    $KOTA_NO=$f['REGISTER_DRNO'];
}



$xml=XML_DATA_NE($kd,$note);

#endregion

#region Degistiriliyor

$xml = str_replace("###SenderID###", $senderID, $xml);
$xml = str_replace("###ReceiverID###", $receiverID, $xml);
$xml = str_replace("###DateStampMessage###", $dateStampMessage, $xml);
$xml = str_replace("###VersionID###", $versionID, $xml);
$xml = str_replace("###ControlNumber###", $controlNumber, $xml);
$xml = str_replace("###OrderType###", $orderType, $xml);
$xml = str_replace("###DocumentNumber###", uniqid(), $xml);
$xml = str_replace("###DateStamp###", $dateStamp, $xml);
$xml = str_replace("###ReferenceNumber###", $KOTA_NO, $xml);
$xml = str_replace("###PartnerNumberSupplierDistributor###", $partnerNumberSupplierDistributor, $xml);
$xml = str_replace("###PartnerName1Distributor###", $partnerName1Distributor, $xml);
$xml = str_replace("###PartnerAddressDistributor###", $partnerAddressDistributor, $xml);
$xml = str_replace("###PartnerPostalCodeDistributor###", $partnerPostalCodeDistributor, $xml);
$xml = str_replace("###PartnerCityDistributor###", $partnerCityDistributor, $xml);
$xml = str_replace("###PartnerCountryCodeDistributor###", $partnerCountryCodeDistributor, $xml);
$xml = str_replace("###ContactNameDistributor###", $contactNameDistributor, $xml);
$xml = str_replace("###ContactPhoneDistributor###", $contactPhoneDistributor, $xml);
$xml = str_replace("###ContactFaxDistributor###", $contactFaxDistributor, $xml);
$xml = str_replace("###ContactMailDistributor###", $contactMailDistributor, $xml);
$xml = str_replace("###PartnerNumberSupplierDeliveryParty###", $partnerNumberSupplierDeliveryParty, $xml);
$xml = str_replace("###PartnerNumberDUNSDeliveryParty###", $partnerNumberDUNSDeliveryParty, $xml);
$xml = str_replace("###PartnerName1DeliveryParty###", $partnerName1DeliveryParty, $xml);
$xml = str_replace("###PartnerAddressDeliveryParty###", $partnerAddressDeliveryParty, $xml);
$xml = str_replace("###PartnerPostalCodeDeliveryParty###", $partnerPostalCodeDeliveryParty, $xml);
$xml = str_replace("###PartnerCityDeliveryParty###", $partnerCityDeliveryParty, $xml);
$xml = str_replace("###PartnerCountryCodeDeliveryParty###", $partnerCountryCodeDeliveryParty, $xml);
$xml = str_replace("###ContactNameDeliveryParty###", $contactNameDeliveryParty, $xml);
$xml = str_replace("###ContactPhoneDeliveryParty###", $contactPhoneDeliveryParty, $xml);
$xml = str_replace("###ContactMailDeliveryParty###", $contactMailDeliveryParty, $xml);
$xml = str_replace("###PartnerNumberSupplierReseller###", $partnerNumberSupplierReseller, $xml);
$xml = str_replace("###PartnerName1Reseller###", $partnerName1Reseller, $xml);
$xml = str_replace("###PartnerAddressReseller###", $partnerAddressReseller, $xml);
$xml = str_replace("###PartnerPostalCodeReseller###", $partnerPostalCodeReseller, $xml);
$xml = str_replace("###PartnerCityReseller###", $partnerCityReseller, $xml);
$xml = str_replace("###PartnerCountryCodeReseller###", $partnerCountryCodeReseller, $xml);
$xml = str_replace("###ContactNameReseller###", $contactNameReseller, $xml);
$xml = str_replace("###ContactPhoneReseller###", $contactPhoneReseller, $xml);
$xml = str_replace("###ContactFaxReseller###", $contactFaxReseller, $xml);
$xml = str_replace("###ContactMailReseller###", $contactMailReseller, $xml);
$xml = str_replace("###PartnerNumberSupplierEndUser###", $partnerNumberSupplierEndUser, $xml);
$xml = str_replace("###PartnerNumberDistributorEndUser###", $partnerNumberDistributorEndUser, $xml);
$xml = str_replace("###PartnerName1EndUser###", $partnerName1EndUser, $xml);
$xml = str_replace("###PartnerAddressEndUser###", $partnerAddressEndUser, $xml);
$xml = str_replace("###PartnerPostalCodeEndUser###", $partnerPostalCodeEndUser, $xml);
$xml = str_replace("###PartnerCityEndUser###", $partnerCityEndUser, $xml);
$xml = str_replace("###PartnerStateEndUser###", $partnerStateEndUser, $xml);
$xml = str_replace("###PartnerCountryCodeEndUser###", $partnerCountryCodeEndUser, $xml);
$xml = str_replace("###ContactNameEndUser###", $contactNameEndUser, $xml);
$xml = str_replace("###ContactPhoneEndUser###", $contactPhoneEndUser, $xml);
$xml = str_replace("###ContactMailEndUser###", $contactMailEndUser, $xml);

$xml = str_replace("###PartnerNumberDUNSDeliveryParty###", $partnerNumberDUNSDeliveryParty, $xml);
$xml = str_replace("###PartnerName1DeliveryParty###", $partnerName1DeliveryParty, $xml);
$xml = str_replace("###PartnerAddressDeliveryParty###", $partnerAddressDeliveryParty, $xml);
$xml = str_replace("###PartnerPostalCodeDeliveryParty###", $partnerPostalCodeDeliveryParty, $xml);
$xml = str_replace("###PartnerCityDeliveryParty###", $partnerCityDeliveryParty, $xml);
$xml = str_replace("###PartnerCountryCodeDeliveryParty###", $partnerCountryCodeDeliveryParty, $xml);
$xml = str_replace("###ContactNameDeliveryParty###", $contactNameDeliveryParty, $xml);
$xml = str_replace("###ContactPhoneDeliveryParty###", $contactPhoneDeliveryParty, $xml);
$xml = str_replace("###ContactMailDeliveryParty###", $contactMailDeliveryParty, $xml);
$xml = str_replace("###PartnerNumberSupplierReseller###", $partnerNumberSupplierReseller, $xml);
$xml = str_replace("###PartnerName1Reseller###", $partnerName1Reseller, $xml);
$xml = str_replace("###PartnerAddressReseller###", $partnerAddressReseller, $xml);
$xml = str_replace("###PartnerPostalCodeReseller###", $partnerPostalCodeReseller, $xml);
$xml = str_replace("###PartnerCityReseller###", $partnerCityReseller, $xml);
$xml = str_replace("###PartnerCountryCodeReseller###", $partnerCountryCodeReseller, $xml);
$xml = str_replace("###ContactNameReseller###", $contactNameReseller, $xml);
$xml = str_replace("###ContactPhoneReseller###", $contactPhoneReseller, $xml);
$xml = str_replace("###ContactFaxReseller###", $contactFaxReseller, $xml);
$xml = str_replace("###ContactMailReseller###", $contactMailReseller, $xml);
$xml = str_replace("###PartnerNumberSupplierEndUser###", $partnerNumberSupplierEndUser, $xml);
$xml = str_replace("###PartnerNumberDistributorEndUser###", $partnerNumberDistributorEndUser, $xml);
$xml = str_replace("###PartnerName1EndUser###", $partnerName1EndUser, $xml);
$xml = str_replace("###PartnerAddressEndUser###", $partnerAddressEndUser, $xml);
$xml = str_replace("###PartnerPostalCodeEndUser###", $partnerPostalCodeEndUser, $xml);
$xml = str_replace("###PartnerCityEndUser###", $partnerCityEndUser, $xml);
$xml = str_replace("###PartnerStateEndUser###", $partnerStateEndUser, $xml);
$xml = str_replace("###PartnerCountryCodeEndUser###", $partnerCountryCodeEndUser, $xml);
$xml = str_replace("###ContactNameEndUser###", $contactNameEndUser, $xml);
$xml = str_replace("###ContactPhoneEndUser###", $contactPhoneEndUser, $xml);
$xml = str_replace("###ContactMailEndUser###", $contactMailEndUser, $xml);
$xml = str_replace("###CompleteDelivery###", $completeDelivery, $xml);
$xml = str_replace("###ShippingTerms###", $shippingTerms, $xml);
$xml = str_replace("###CompleteDelivery###", $completeDelivery, $xml);
$xml = str_replace("###ShippingMethod###", $shippingMethod, $xml);
$xml = str_replace("###ShippingAccount###", $shippingAccount, $xml);

#endregion

#region Cikti

#endregion

$trace>0 ? print_r($xml) : null ;

$trace>0 ? die() : null ;

$url="http://httpnatest.sterlingcommerce.com/http?HTTPUsername=KOMToIBM&HTTPPassword=Welcome2ibm";

//$url='http://176.236.6.234:54054/_service/sps/?username=IBMToKOM&password=Welcome2Komtera';
//$url='http://172.16.84.214/_service/sps/?username=IBMToKOM&password=Welcome2Komtera';

$headers = array(
    'Content-Type: application/xml',
    'Content-Length: ' . strlen($xml)
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    echo 'HTTP isteği başarısız.';
} else {
    echo $response;
}

$stmt = $conn->prepare("insert into aa_erp_kt_edi (xmldata,in_out,cevap,belge_no) values (:xml,'out','$response','$senderID')");
$stmt->execute([':xml'=>$xml]);

//file_put_contents('d:\log.txt', Date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
//file_put_contents('d:\log.txt', $xml . PHP_EOL, FILE_APPEND);
//file_put_contents('d:\log.txt', $response . PHP_EOL, FILE_APPEND);

#region Trace yazilari

if ($trace==1) {
    echo $xml;
}

#endregion
?>