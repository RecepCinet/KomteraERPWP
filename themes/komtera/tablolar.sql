create table atest_aa_erp_borsa
(
    id     int identity
        constraint PK_aatest_aa_erp_borsa
            primary key,
    borsa  nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    rank   int,
    para   nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    fiyat  money,
    volume bigint
)
    go

create table atest_aa_erp_il_ilce
(
    id   int identity
        constraint PK_atest_aa_erp_il_ilce
            primary key,
    il   nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    ilce nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_Acronis_CH_Eslesme
(
    id                 int identity
        constraint PK_atest_aa_erp_kt_Acronis_CH_Eslesme
            primary key,
    acronis_bayi_adi   nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    komtera_bayi_adi   nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    musteri_temsilcisi nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_acronis_fatura_kes
(
    id           int identity
        constraint PK_atest_aa_erp_kt_acronis_fatura_kes
            primary key,
    acronis_bayi nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    eposta       nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    sku          nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    adet         int,
    birim        money,
    tutar        money,
    vade         int
)
    go

create table atest_aa_erp_kt_aktiviteler
(
    id                 int identity
        constraint PK_atest_aa_erp_kt_aktiviteler
            primary key,
    CN                 nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_TEMSILCISI nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    BU                 nvarchar(3) collate SQL_Latin1_General_CP1254_CI_AS,
    TIP                nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    TIP_BAYI           nvarchar(12) collate SQL_Latin1_General_CP1254_CI_AS,
    TIP_KIMILE         nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_MEVCUT        nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_MEVCUT_CHKOD  nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_YENI          nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI            nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    URETICI            nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    ETKINLIK           text collate SQL_Latin1_General_CP1254_CI_AS,
    TIP_KIMILE_BAYI    text collate SQL_Latin1_General_CP1254_CI_AS,
    TIP_KIMILE_URETICI text collate SQL_Latin1_General_CP1254_CI_AS,
    TARIH              date
        constraint DF_atest_aa_erp_kt_aktiviteler_TARIH default getdate(),
    SURE               int,
    SEHIR              nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    GORUSME_KISI       nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    ACIKLAMA           text collate SQL_Latin1_General_CP1254_CI_AS,
    KILIT              int,
    NOTLAR             text collate SQL_Latin1_General_CP1254_CI_AS,
    SIL                int
        constraint DF_atest_aa_erp_kt_aktiviteler_SIL default 0
)
    go

create table atest_aa_erp_kt_ayarlar_onaylar
(
    id       int identity
        constraint PK_atest_aa_erp_kt_ayarlar_onaylar
            primary key,
    kural    nvarchar(90) collate SQL_Latin1_General_CP1254_CI_AS,
    aciklama nvarchar(80) collate SQL_Latin1_General_CP1254_CI_AS,
    kim      nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_ayarlar_onaylar_kar
(
    id           int identity
        constraint PK_atest_aa_erp_kt_ayarlar_onaylar_kar
            primary key,
    marka        nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    seviye       int,
    bayi_ch_kodu nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    onay1_oran   float,
    onay1_mail   nvarchar(60) collate SQL_Latin1_General_CP1254_CI_AS,
    onay2_oran   float,
    onay2_mail   nvarchar(60) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_bankalar
(
    id    int identity
        constraint PK_atest_aa_erp_kt_bankalar
            primary key,
    sira  int,
    banka nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    iban  nvarchar(60) collate SQL_Latin1_General_CP1254_CI_AS,
    kur   nvarchar(3) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_bayiler_eskiseviye
(
    id      int identity
        constraint PK_atest_aa_erp_kt_bayiler_eskiseviye
            primary key,
    CH_KODU nvarchar(17) collate SQL_Latin1_General_CP1254_CI_AS,
    marka   nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    seviye  int
)
    go

create table atest_aa_erp_kt_bayiler_kara_liste
(
    id             int identity
        constraint PK_atest_aa_erp_kt_bayiler_kara_liste
            primary key,
    ch_kodu        nvarchar(16) collate SQL_Latin1_General_CP1254_CI_AS,
    dikkat_listesi int,
    kara_liste     int
)
    go

create table atest_aa_erp_kt_bayiler_markaseviyeleri
(
    id      int identity
        constraint PK_atest_aa_erp_kt_bayiler_markaseviyeleri
            primary key,
    CH_KODU nvarchar(17) collate SQL_Latin1_General_CP1254_CI_AS,
    marka   nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    seviye  int
)
    go

create table atest_aa_erp_kt_bayiler_yetkililer
(
    id      int identity
        constraint PK_atest_aa_erp_kt_bayiler_yetkililer
            primary key,
    CH_KODU nvarchar(17) collate SQL_Latin1_General_CP1254_CI_AS,
    yetkili nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    telefon nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    eposta  nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_demolar
(
    id                 bigint identity
        constraint PK_atest_aa_erp_kt_demolar
            primary key,
    LOGICALREF         nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    SKU                nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    ACIKLAMA           nchar(150) collate SQL_Latin1_General_CP1254_CI_AS,
    MARKA              nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    SERIAL_NO          nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    FIRSAT_NO          nvarchar(10)
        constraint DF__atest_aa_erp_kt__FIRSA__6742DBF5 default NULL collate SQL_Latin1_General_CP1254_CI_AS,
    CD                 date
        constraint DF_atest_aa_erp_kt_demolar_CD default getdate(),
    CN                 nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_TEMSILCISI nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    BITIS_TARIHI       date,
    BAYI_CHKODU        nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI               nvarchar(150) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYININ_MUSTERISI  nvarchar(150) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_YETKILI       nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_TELEFON       nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_EPOSTA        nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    DEMO_DURUM         int,
    MUSTERI_YETKILI    nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_TELEFON    nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_EPOSTA     nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    ADRES              nvarchar(200) collate SQL_Latin1_General_CP1254_CI_AS,
    ADRES_ILCE         nvarchar(22) collate SQL_Latin1_General_CP1254_CI_AS,
    ADRES_SEHIR        nvarchar(22) collate SQL_Latin1_General_CP1254_CI_AS,
    SIL                bit
        constraint DF__atest_aa_erp_kt_d__SIL__4FAB4986 default 0,
    KARGO_NO           nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    KARGO_DURUM        nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS,
    DEMO_NE            nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    TESLIMAT_KIME      nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    KARGO_OLUSTUMU     int
        constraint DF_atest_aa_erp_kt_demolar_KARGO_OLUSTUMU default 0,
    YAZISMALAR         text collate SQL_Latin1_General_CP1254_CI_AS,
    KAPANDI            int
        constraint DF_atest_aa_erp_kt_demolar_KAPANDI default 0,
    KARGO_URL          text collate SQL_Latin1_General_CP1254_CI_AS,
    ELDEN_TESLIM_ALAN  nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    IRSALIYE_NO        nvarchar(35) collate SQL_Latin1_General_CP1254_CI_AS,
    GONDERILME_NEDENI  nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    GONDERILME_KIME    nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    ELDEN_TESLIM_MI    int
)
    go

create table atest_aa_erp_kt_demolar_skular
(
    id        bigint identity
        constraint PK_atest_aa_erp_kt_demolar_skular
            primary key,
    demo_id   int,
    SKU       nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    marka     varchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    aciklama  varchar(150) collate SQL_Latin1_General_CP1254_CI_AS,
    serial_no varchar(25) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_edi
(
    ID       int identity
        constraint PK_atest_aa_ERP_KT_EDI
            primary key,
    cd       datetime default getdate(),
    xmldata  text collate SQL_Latin1_General_CP1254_CI_AS,
    belge_no nvarchar(32) collate SQL_Latin1_General_CP1254_CI_AS,
    cevap    text collate SQL_Latin1_General_CP1254_CI_AS,
    in_out   nvarchar(3) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_etkinlikler
(
    id        int identity
        constraint PK_atest_aa_erp_kt_etkinlikler
            primary key,
    marka     nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    baslik    nvarchar(150) collate SQL_Latin1_General_CP1254_CI_AS,
    kodu      nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    tarih_bas date,
    tarih_bit date
)
    go

create table atest_aa_erp_kt_fatura_i
(
    id               int identity
        constraint PK_atest_aa_ERP_KT_FATURA_I
            primary key,
    _cd              datetime default getdate(),
    _teklif_no       nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    _faturami        tinyint  default 0,
    _status_i        tinyint  default 0,
    _status_f        tinyint  default 0,
    siparisNo        nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    faturaTarihi     datetime default getdate(),
    irsaliyeTarihi   datetime default getdate(),
    cariKod          nvarchar(18) collate SQL_Latin1_General_CP1254_CI_AS,
    projeKodu        nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    vadeKodu         nvarchar(5) collate SQL_Latin1_General_CP1254_CI_AS,
    satisElemanKodu  nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    dovizTuru        nvarchar(3) collate SQL_Latin1_General_CP1254_CI_AS,
    dovizKuru        money    default 0,
    unvan            nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    kisiBilgi        nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    adres            nvarchar(150) collate SQL_Latin1_General_CP1254_CI_AS,
    musteriSiparisNo nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    ambarKodu        int      default 0,
    r_FisNo          nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    r_LogoId         int,
    r_result         nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    r_response       text collate SQL_Latin1_General_CP1254_CI_AS,
    dummy1           nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    fonGeliri        money,
    fonHarcama       money
)
    go

create table atest_aa_erp_kt_fatura_kes_sophos
(
    id         int identity
        constraint PK_atest_aa_erp_kt_fatura_kes_sophos
            primary key,
    CHKODU     nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    VADE       int,
    MT         nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    SKU        nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    ADET       int,
    BIRIM      money,
    TOPLAM     money,
    FATURA_NOT text collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_fatura_urunler_i
(
    id           int identity
        constraint PK_atest_aa_ERP_KT_FATURA_URUNLER_I
            primary key,
    _cd          datetime     default getdate(),
    _x_teklif_no nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    _x_siparisNo nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    _status      int          default 0,
    kod          nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    malzemeTip   int          default 1,
    birim        nvarchar(10) default N'Adet' collate SQL_Latin1_General_CP1254_CI_AS collate SQL_Latin1_General_CP1254_CI_AS,
    miktar       int,
    birimFiyat   money,
    kdvOran      int          default 18,
    seriNo       nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    lisansSuresi nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    projeKodu    nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    dummy1       nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    dummy2       nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    hizmet       bit          default 0
)
    go

create table atest_aa_erp_kt_firsatlar
(
    id                      bigint identity (212345, 1)
        constraint PK_atest_aa_erp_kt_firsatlar
            primary key,
    FIRSAT_NO               nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    BASLANGIC_TARIHI        date,
    BITIS_TARIHI            date,
    REVIZE_TARIHI           date,
    KAYIDI_ACAN             nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_TEMSILCISI      nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    MARKA                   nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    MARKA_BAYI_SEVIYE       nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_ADI                nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_CHKODU             nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_YETKILI_ISIM       nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_YETKILI_TEL        nvarchar(11) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_YETKILI_EPOSTA     nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_ADI             nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_YETKILI_ISIM    nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_YETKILI_TEL     nvarchar(11) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_YETKILI_EPOSTA  nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_ADRES              nvarchar(201) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_SEHIR              nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_ILCE               nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    GELIS_KANALI            nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    OLASILIK                nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    DURUM                   nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    KAYBEDILME_NEDENI       nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    TAHMINI_TUTAR           money,
    TAHMINI_TUTAR_K         money,
    REGISTER                nvarchar collate SQL_Latin1_General_CP1254_CI_AS,
    REGISTER_DRNO           text collate SQL_Latin1_General_CP1254_CI_AS,
    SIL                     int
        constraint DF_atest_aa_erp_kt_firsatlar_SILINMIS default 0,
    ARSIVLENMIS             int
        constraint DF_atest_aa_erp_kt_firsatlar_ARSIVLENMIS default 0,
    CN                      nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    CT                      time
        constraint DF_atest_aa_erp_kt_firsatlar_CT default getdate(),
    PARA_BIRIMI             nvarchar(3) collate SQL_Latin1_General_CP1254_CI_AS,
    VADE                    nvarchar(5) collate SQL_Latin1_General_CP1254_CI_AS,
    SIPARIS                 int,
    SEVKIYAT_ADRES          nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    SEVKIYAT_IL             nvarchar(14) collate SQL_Latin1_General_CP1254_CI_AS,
    SEVKIYAT_ILCE           nvarchar(16) collate SQL_Latin1_General_CP1254_CI_AS,
    SEVKIYAT_PARCALI        int
        constraint DF_atest_aa_erp_kt_firsatlar_SEVKIYAT_PARCALI default 0,
    SEVKIYAT_KIME           int
        constraint DF_atest_aa_erp_kt_firsatlar_SEVKIYAT_KIME default 0,
    FATURA_PARCALI          int
        constraint DF_atest_aa_erp_kt_firsatlar_FATURA_PARCALI default 0,
    ETKINLIK                text collate SQL_Latin1_General_CP1254_CI_AS,
    KAYBEDILME_NEDENI_DIGER text collate SQL_Latin1_General_CP1254_CI_AS,
    MARKA_MANAGER           nvarchar(30) collate SQL_Latin1_General_CP1254_CI_AS,
    MARKA_MANAGER_EPOSTA    nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    BAGLI_FIRSAT_NO         nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    PROJE_ADI               nvarchar(150) collate SQL_Latin1_General_CP1254_CI_AS,
    FIRSAT_ACIKLAMA         text collate SQL_Latin1_General_CP1254_CI_AS,
    FIRSAT_ANA              tinyint,
    BITIS_TARIHI_NOTU       text collate SQL_Latin1_General_CP1254_CI_AS,
    BITIS_NOTU_COUNT        int,
    BAYI_POSTA_KODU         nvarchar(5) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create index atest_aa_erp_kt_firsatlar_FIRSAT_NO_IDX
    on atest_aa_erp_kt_firsatlar (FIRSAT_NO)
    go

create index atest_aa_erp_kt_firsatlar_BASLANGIC_TARIHI_IDX
    on atest_aa_erp_kt_firsatlar (BASLANGIC_TARIHI)
    go

create index atest_aa_erp_kt_firsatlar_DURUM_IDX
    on atest_aa_erp_kt_firsatlar (DURUM)
    go

create index IX_atest_aa_erp_kt_firsatlar_FIRSAT_NO
    on atest_aa_erp_kt_firsatlar (FIRSAT_NO)
    go

create index IDX_FIRSAT_NO
    on atest_aa_erp_kt_firsatlar (FIRSAT_NO)
    go

create index IDX_DURUM_SIL
    on atest_aa_erp_kt_firsatlar (DURUM, SIL)
    go

create index IDX_MARKA
    on atest_aa_erp_kt_firsatlar (MARKA)
    go

create index IDX_FIRSAT_ANA_BAGLI_FIRSAT
    on atest_aa_erp_kt_firsatlar (FIRSAT_ANA, BAGLI_FIRSAT_NO)
    go

create index idx_firsat_details
    on atest_aa_erp_kt_firsatlar (FIRSAT_ANA, BAGLI_FIRSAT_NO)
    go

create table atest_aa_erp_kt_firsatlar_backup
(
    id                      bigint identity (212345, 1),
    FIRSAT_NO               nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    BASLANGIC_TARIHI        date,
    BITIS_TARIHI            date,
    REVIZE_TARIHI           date,
    KAYIDI_ACAN             nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_TEMSILCISI      nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    MARKA                   nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    MARKA_BAYI_SEVIYE       nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_ADI                nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_CHKODU             nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_YETKILI_ISIM       nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_YETKILI_TEL        nvarchar(11) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_YETKILI_EPOSTA     nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_ADI             nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_YETKILI_ISIM    nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_YETKILI_TEL     nvarchar(11) collate SQL_Latin1_General_CP1254_CI_AS,
    MUSTERI_YETKILI_EPOSTA  nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_ADRES              nvarchar(201) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_SEHIR              nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    BAYI_ILCE               nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    GELIS_KANALI            nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    OLASILIK                nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    DURUM                   nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    KAYBEDILME_NEDENI       nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    TAHMINI_TUTAR           money,
    TAHMINI_TUTAR_K         money,
    REGISTER                nvarchar collate SQL_Latin1_General_CP1254_CI_AS,
    REGISTER_DRNO           text collate SQL_Latin1_General_CP1254_CI_AS,
    SIL                     int,
    ARSIVLENMIS             int,
    CN                      nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    CT                      time,
    PARA_BIRIMI             nvarchar(3) collate SQL_Latin1_General_CP1254_CI_AS,
    VADE                    nvarchar(5) collate SQL_Latin1_General_CP1254_CI_AS,
    SIPARIS                 int,
    SEVKIYAT_ADRES          nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    SEVKIYAT_IL             nvarchar(14) collate SQL_Latin1_General_CP1254_CI_AS,
    SEVKIYAT_ILCE           nvarchar(16) collate SQL_Latin1_General_CP1254_CI_AS,
    SEVKIYAT_PARCALI        int,
    SEVKIYAT_KIME           int,
    FATURA_PARCALI          int,
    ETKINLIK                text collate SQL_Latin1_General_CP1254_CI_AS,
    KAYBEDILME_NEDENI_DIGER text collate SQL_Latin1_General_CP1254_CI_AS,
    MARKA_MANAGER           nvarchar(30) collate SQL_Latin1_General_CP1254_CI_AS,
    MARKA_MANAGER_EPOSTA    nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    BAGLI_FIRSAT_NO         nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    PROJE_ADI               nvarchar(150) collate SQL_Latin1_General_CP1254_CI_AS,
    FIRSAT_ACIKLAMA         text collate SQL_Latin1_General_CP1254_CI_AS,
    FIRSAT_ANA              tinyint,
    BITIS_TARIHI_NOTU       text collate SQL_Latin1_General_CP1254_CI_AS,
    BITIS_NOTU_COUNT        int,
    BAYI_POSTA_KODU         nvarchar(5) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_fiyat_listesi
(
    id                int identity
        constraint PK_atest_aa_erp_kt_fiyat_listesi
            primary key,
    sku               nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    urunAciklama      nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS,
    marka             nvarchar(35) collate SQL_Latin1_General_CP1254_CI_AS,
    tur               nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    cozum             nvarchar(250) collate SQL_Latin1_General_CP1254_CI_AS,
    lisansSuresi      int,
    paraBirimi        nvarchar(3) collate SQL_Latin1_General_CP1254_CI_AS,
    listeFiyati       money,
    listeFiyatiUpLift money,
    wgCategory        nvarchar(5) collate SQL_Latin1_General_CP1254_CI_AS,
    wgUpcCode         nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    a_iskonto4        float,
    a_iskonto3        float,
    a_iskonto2        float,
    a_iskonto1        float,
    s_iskonto4        float,
    s_iskonto3        float,
    s_iskonto2        float,
    s_iskonto1        float,
    a_iskonto4_r      float,
    a_iskonto3_r      float,
    a_iskonto2_r      float,
    a_iskonto1_r      float,
    s_iskonto4_r      float,
    s_iskonto3_r      float,
    s_iskonto2_r      float,
    s_iskonto1_r      float
)
    go

create index atest_aa_erp_kt_fiyat_listesi_marka_IDX
    on atest_aa_erp_kt_fiyat_listesi (marka)
    go

create index atest_aa_erp_kt_fiyat_listesi_sku_IDX
    on atest_aa_erp_kt_fiyat_listesi (sku)
    go

create index IDX_SKU_FL
    on atest_aa_erp_kt_fiyat_listesi (sku)
    go

create table atest_aa_erp_kt_is_atama
(
    id       int identity
        constraint PK_atest_aa_erp_kt_is_atama
            primary key,
    cd       date
        constraint DF_atest_aa_erp_kt_is_atama_cd default getdate(),
    ct       time
        constraint DF_atest_aa_erp_kt_is_atama_ct default getdate(),
    modul    nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    mid      nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    kimden   nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    kime     nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    beklenen nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_kampanyalar
(
    id        int identity
        constraint PK_atest_aa_erp_kt_kampanyalar
            primary key,
    marka     nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    baslik    nvarchar(150) collate SQL_Latin1_General_CP1254_CI_AS,
    kodu      nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    tarih_bas date,
    tarih_bit date
)
    go

create table atest_aa_erp_kt_log
(
    id               int identity
        primary key,
    tarih            datetime default getdate()                            not null,
    modul            nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS not null,
    kullanici        nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS not null,
    yapilan_islem    nvarchar(500) collate SQL_Latin1_General_CP1254_CI_AS not null,
    detay            nvarchar(max) collate SQL_Latin1_General_CP1254_CI_AS,
    ip_adres         nvarchar(45) collate SQL_Latin1_General_CP1254_CI_AS,
    olusturma_tarihi datetime default getdate()                            not null
)
    go

create index IX_atest_aa_erp_kt_log_tarih
    on atest_aa_erp_kt_log (tarih)
    go

create index IX_atest_aa_erp_kt_log_modul
    on atest_aa_erp_kt_log (modul)
    go

create index IX_atest_aa_erp_kt_log_kullanici
    on atest_aa_erp_kt_log (kullanici)
    go

create table atest_aa_erp_kt_markalar
(
    id    int identity
        constraint PK_atest_aa_erp_kt_markalar
            primary key,
    marka nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    d1    float
)
    go

create table atest_aa_erp_kt_markalar_managers
(
    id      int identity
        constraint atest_aa_erp_kt_markalar_managers_pk
            primary key,
    marka   nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    yetkili nvarchar(35) collate SQL_Latin1_General_CP1254_CI_AS,
    telefon nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    eposta  nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    istek   int
)
    go

create unique index atest_aa_erp_kt_markalar_managers_id_uindex
    on atest_aa_erp_kt_markalar_managers (id)
    go

create table atest_aa_erp_kt_mcafee_kotasyon
(
    id       int identity,
    f1       text collate SQL_Latin1_General_CP1254_CI_AS,
    f2       text collate SQL_Latin1_General_CP1254_CI_AS,
    f3       text collate SQL_Latin1_General_CP1254_CI_AS,
    f4       text collate SQL_Latin1_General_CP1254_CI_AS,
    f5       text collate SQL_Latin1_General_CP1254_CI_AS,
    f6       text collate SQL_Latin1_General_CP1254_CI_AS,
    f7       text collate SQL_Latin1_General_CP1254_CI_AS,
    f8       text collate SQL_Latin1_General_CP1254_CI_AS,
    f9       text collate SQL_Latin1_General_CP1254_CI_AS,
    f10      text collate SQL_Latin1_General_CP1254_CI_AS,
    f11      text collate SQL_Latin1_General_CP1254_CI_AS,
    f12      text collate SQL_Latin1_General_CP1254_CI_AS,
    f13      text collate SQL_Latin1_General_CP1254_CI_AS,
    f14      text collate SQL_Latin1_General_CP1254_CI_AS,
    f15      text collate SQL_Latin1_General_CP1254_CI_AS,
    f16      text collate SQL_Latin1_General_CP1254_CI_AS,
    f17      text collate SQL_Latin1_General_CP1254_CI_AS,
    f18      text collate SQL_Latin1_General_CP1254_CI_AS,
    f19      text collate SQL_Latin1_General_CP1254_CI_AS,
    f20      text collate SQL_Latin1_General_CP1254_CI_AS,
    f21      text collate SQL_Latin1_General_CP1254_CI_AS,
    f22      text collate SQL_Latin1_General_CP1254_CI_AS,
    f23      text collate SQL_Latin1_General_CP1254_CI_AS,
    f24      text collate SQL_Latin1_General_CP1254_CI_AS,
    f25      text collate SQL_Latin1_General_CP1254_CI_AS,
    username text collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_mcafee_sku_sure
(
    id  int identity
        constraint PK_atest_aa_erp_kt_mcafee_sku_sure
            primary key,
    SKU nvarchar(70) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_mediamarkt_faturalama
(
    id          int identity
        constraint PK_atest_aa_erp_kt_mediamarkt_faturalama
            primary key,
    magaza      nvarchar(max) collate SQL_Latin1_General_CP1254_CI_AS,
    adres       text collate SQL_Latin1_General_CP1254_CI_AS,
    sehir       text collate SQL_Latin1_General_CP1254_CI_AS,
    sku         text collate SQL_Latin1_General_CP1254_CI_AS,
    adet        int,
    birim_fiyat money,
    belge_no    nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    dummy1      nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    dummy2      nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_mt_hedefler
(
    id    int identity
        constraint PK_atest_aa_erp_kt_mt_hedefler
            primary key,
    marka nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    q1    money,
    q2    money,
    q3    money,
    q4    money
)
    go

create table atest_aa_erp_kt_musteriler
(
    musteri    nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    id         int identity,
    adres      nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    sehir      nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    posta_kodu nvarchar(5) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create index IX_atest_aa_erp_kt_id
    on atest_aa_erp_kt_musteriler (musteri)
    go

create table atest_aa_erp_kt_musteriler_yetkililer
(
    id         int identity
        constraint PK_atest_aa_erp_kt_musteriler_yetkililer
            primary key,
    musteri_id int,
    yetkili    nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    telefon    nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    eposta     nvarchar(75) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_onay_bekleyenler
(
    id    int identity
        constraint PK_atest_aa_erp_kt_onay_bekleyenler
            primary key,
    zaman datetime
        constraint DF_atest_aa_erp_kt_onay_bekleyenler_zaman default getdate()
)
    go

create table atest_aa_erp_kt_poc
(
    id        bigint identity (11, 123456)
        constraint PK_atest_aa_erp_kt_poc
            primary key,
    FIRSAT_NO nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    TUR       nvarchar(4) collate SQL_Latin1_General_CP1254_CI_AS,
    CD        date,
    CT        time,
    CR        nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_poc_emek
(
    id       int identity
        constraint PK_atest_aa_erp_kt_poc_emek
            primary key,
    x_poc_id nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    NEREDE   nvarchar(7) collate SQL_Latin1_General_CP1254_CI_AS,
    MUHENDIS nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    IL       nvarchar(14) collate SQL_Latin1_General_CP1254_CI_AS,
    ILCE     nvarchar(16) collate SQL_Latin1_General_CP1254_CI_AS,
    TARIH    date,
    SURE     int
)
    go

create table atest_aa_erp_kt_pref
(
    id                    int identity
        constraint PK_atest_aa_erp_kt_pref
            primary key,
    ayar                  varchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    deger                 varchar(200) collate SQL_Latin1_General_CP1254_CI_AS,
    lisans_tarih_markalar varchar(200) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_siparisler
(
    id                  int identity
        constraint PK_atest_aa_erp_kt_siparisler
            primary key,
    CD                  date
        constraint DF_atest_aa_erp_kt_siparisler_CD default getdate(),
    CT                  time
        constraint DF_atest_aa_erp_kt_siparisler_CT default getdate(),
    FATURALAMA_TARIHI   date,
    SIPARIS_NO          nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    PARCA               int
        constraint DF_atest_aa_erp_kt_siparisler_PARCA default 1,
    SIPARIS_DURUM       int
        constraint DF_atest_aa_erp_kt_siparisler_SIPARIS_DURUM default 0,
    SIPARIS_DURUM_ALT   int,
    X_TEKLIF_NO         nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    X_FIRSAT_NO         nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    FATURA_BASILDI      int
        constraint DF_atest_aa_erp_kt_siparisler_FATURA_BASILDI default 0,
    FATURA_NO           nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    IRSALIYE_NO         nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    YURTDISI_SIPARIS    int
        constraint DF_atest_aa_erp_kt_siparisler_YURTDISI_SIPARIS default 0,
    KOTASYON_BEKLENIYOR int
        constraint DF_atest_aa_erp_kt_siparisler_KOTASYON_BEKLENIYOR default 0,
    MUSTERI_SIPARIS_NO  nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    KARGO_GONDERI_NO    nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    KARGO_API_CEVAP     text collate SQL_Latin1_General_CP1254_CI_AS,
    KARGO_DURUM         nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    KARGO_TESLIM_KISI   nvarchar(30) collate SQL_Latin1_General_CP1254_CI_AS,
    KARGO_TESLIM_TARIHI nvarchar(12) collate SQL_Latin1_General_CP1254_CI_AS,
    KARGO_URL           text collate SQL_Latin1_General_CP1254_CI_AS,
    KARGO_MAIL_DURUM    int
        constraint DF_atest_aa_erp_kt_siparisler_KARGO_MAIL_DURUM default 0,
    OZEL_KUR            money,
    FATURALAMA_SAATI    nvarchar(5) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create index atest_aa_erp_kt_siparisler_SIPARIS_DURUM_IDX
    on atest_aa_erp_kt_siparisler (SIPARIS_DURUM)
    go

create index atest_aa_erp_kt_siparisler_CD_IDX
    on atest_aa_erp_kt_siparisler (CD)
    go

create index IX_atest_aa_erp_kt_siparisler_X_TEKLIF_NO
    on atest_aa_erp_kt_siparisler (X_TEKLIF_NO, SIPARIS_DURUM)
    go

create index IDX_SIPARIS_NO
    on atest_aa_erp_kt_siparisler (SIPARIS_NO)
    go

create index IDX_SIPARIS_DURUM
    on atest_aa_erp_kt_siparisler (SIPARIS_DURUM)
    go

create index IDX_SIPARIS_DURUM_ALT
    on atest_aa_erp_kt_siparisler (SIPARIS_DURUM_ALT)
    go

create index IDX_X_FIRSAT_NO
    on atest_aa_erp_kt_siparisler (X_FIRSAT_NO)
    go

create table atest_aa_erp_kt_siparisler_urunler
(
    id             int identity
        constraint PK_atest_aa_erp_kt_siparisler_urunler
            primary key,
    X_SIPARIS_NO   nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    SIRA           int,
    SKU            nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    ACIKLAMA       text collate SQL_Latin1_General_CP1254_CI_AS,
    TIP            nvarchar(35) collate SQL_Latin1_General_CP1254_CI_AS,
    SURE           int,
    ADET           int,
    BIRIM_FIYAT    money,
    LISANS         nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    SERIAL         nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    SEC            int,
    SEC_ADET       int,
    YENILEMETARIHI date
)
    go

create index IX_atest_aa_erp_kt_siparisler_urunler_X_SIPARIS_NO
    on atest_aa_erp_kt_siparisler_urunler (X_SIPARIS_NO)
    go

create index IDX_X_SIPARIS_NO
    on atest_aa_erp_kt_siparisler_urunler (X_SIPARIS_NO)
    go

create index IDX_TIP_X_SIPARIS_NO
    on atest_aa_erp_kt_siparisler_urunler (TIP, X_SIPARIS_NO)
    go

create table atest_aa_erp_kt_sophos_edi_cari
(
    ID      int identity
        constraint PK_atest_aa_ERP_KT_SOPHOS_EDI_CARI
            primary key,
    cd      datetime default getdate(),
    ACCOUNT nvarchar(35) collate SQL_Latin1_General_CP1254_CI_AS,
    NAME    nvarchar(250) collate SQL_Latin1_General_CP1254_CI_AS,
    EDI_NO  nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    CH_KODU nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    LEVEL   nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_teklif_dosyalar
(
    id            int identity
        primary key,
    TEKLIF_NO     nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS  not null,
    ORIGINAL_NAME nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS not null,
    FILE_NAME     nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS not null,
    FILE_PATH     nvarchar(500) collate SQL_Latin1_General_CP1254_CI_AS not null,
    FILE_SIZE     bigint                                                not null,
    FILE_TYPE     nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS not null,
    UPLOAD_DATE   datetime default getdate()                            not null,
    UPLOADED_BY   nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    SIL           bit      default 0
)
    go

create table atest_aa_erp_kt_teklif_lisans_dosyalar
(
    id            int identity
        primary key,
    TEKLIF_NO     nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS  not null,
    ORIGINAL_NAME nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS not null,
    FILE_NAME     nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS not null,
    FILE_PATH     nvarchar(500) collate SQL_Latin1_General_CP1254_CI_AS not null,
    FILE_SIZE     bigint                                                not null,
    FILE_TYPE     nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS not null,
    UPLOAD_DATE   datetime default getdate()                            not null,
    UPLOADED_BY   nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    SIL           bit      default 0
)
    go

create table atest_aa_erp_kt_teklifler
(
    id                       bigint identity (100000, 11)
        constraint PK_atest_aa_erp_kt_teklifler
            primary key,
    X_FIRSAT_NO              nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    TEKLIF_NO                nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    YARATILIS_TARIHI         date
        constraint DF_atest_aa_erp_kt_teklifler_YARATILIS_TARIHI default getdate(),
    YARATILIS_SAATI          time
        constraint DF_atest_aa_erp_kt_teklifler_YARATILIS_SAATI default getdate(),
    TEKLIF_TIPI              int
        constraint DF_atest_aa_erp_kt_teklifler_TEKLIF_TIPI default 0,
    SATIS_TUTARI             money,
    KILIT                    int,
    SATIS_TIPI               nvarchar(3)
        constraint DF__atest_aa_erp_kt__SATIS__080F9E3A default '0' collate SQL_Latin1_General_CP1254_CI_AS,
    VADE                     nvarchar(5) collate SQL_Latin1_General_CP1254_CI_AS,
    KOMISYON_F1              money,
    KOMISYON_F1_ACIKLAMA     text collate SQL_Latin1_General_CP1254_CI_AS,
    KOMISYON_F2              money,
    KOMISYON_F2_ACIKLAMA     text collate SQL_Latin1_General_CP1254_CI_AS,
    KOMISYON_H               money,
    KOMISYON_H_ACIKLAMA      text collate SQL_Latin1_General_CP1254_CI_AS,
    SEC                      int,
    SIL                      tinyint,
    NOTLAR                   text collate SQL_Latin1_General_CP1254_CI_AS,
    KAMPANYA                 text collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY1                    int,
    ONAY1_KIM                nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY1_ACIKLAMA           nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY2                    int,
    ONAY2_KIM                nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY2_ACIKLAMA           nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    VADE_ONAY                int,
    VADE_ONAY_ACIKLAMA       text collate SQL_Latin1_General_CP1254_CI_AS,
    VADE_ONAY_KIM            text collate SQL_Latin1_General_CP1254_CI_AS,
    TEKLIF_SURE              int
        constraint DF_atest_aa_erp_kt_teklifler_TEKLIF_SURE default 15,
    TEKLIF_EK_NOT            text collate SQL_Latin1_General_CP1254_CI_AS,
    TEKLIF_LISTELI           int,
    t_maliyet                money
        constraint DF__atest_aa_erp_kt__t_mal__7B4A9FF9 default NULL,
    t_satis                  money
        constraint DF__atest_aa_erp_kt__t_sat__7C3EC432 default NULL,
    KOMISYON_TIP1            nvarchar(16) collate SQL_Latin1_General_CP1254_CI_AS,
    KOMISYON_TIP2            nvarchar(16) collate SQL_Latin1_General_CP1254_CI_AS,
    PDF                      int
        constraint DF_atest_aa_erp_kt_teklifler_PDF default 0,
    KOMISYON_ODENDI          int,
    KOMISYON_ODENDI_ACIKLAMA nvarchar(99) collate SQL_Latin1_General_CP1254_CI_AS,
    KOMTERA_HIZMET_BEDELI    money,
    KOMTERA_HIZMET_ADI       nvarchar(30) collate SQL_Latin1_General_CP1254_CI_AS,
    KOMISYON_F3              money,
    KOMISYON_F3_ACIKLAMA     text collate SQL_Latin1_General_CP1254_CI_AS,
    KILIT_TARIHI             date,
    yenileme_log             nvarchar(30) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create index [NonClusteredIndex-20220701-131306]
    on atest_aa_erp_kt_teklifler (X_FIRSAT_NO, TEKLIF_TIPI)
    go

create index atest_aa_erp_kt_teklifler_TEKLIF_NO_IDX
    on atest_aa_erp_kt_teklifler (TEKLIF_NO)
    go

create index IX_atest_aa_erp_kt_teklifler_TEKLIF_NO
    on atest_aa_erp_kt_teklifler (TEKLIF_NO)
    go

create index IDX_TEKLIF_NO
    on atest_aa_erp_kt_teklifler (TEKLIF_NO)
    go

create index IDX_X_FIRSAT_NO
    on atest_aa_erp_kt_teklifler (X_FIRSAT_NO)
    go

create index IDX_TEKLIF_TIPI_PDF
    on atest_aa_erp_kt_teklifler (TEKLIF_TIPI, PDF)
    go

create index IDX_TEKLIF_KOMISYON_MALIYET_SATIS
    on atest_aa_erp_kt_teklifler (t_maliyet, t_satis, KOMISYON_H, KOMISYON_F1, KOMISYON_F2)
    go

create index idx_teklif_tipi
    on atest_aa_erp_kt_teklifler (TEKLIF_TIPI)
    go

create index atest_aa_erp_kt_teklifler_SATIS_TIPI_index
    on atest_aa_erp_kt_teklifler (SATIS_TIPI)
    go

create index IX_atest_aa_erp_kt_teklifler_YARATILIS_TARIHI
    on atest_aa_erp_kt_teklifler (YARATILIS_TARIHI) include (TEKLIF_NO, TEKLIF_TIPI)
go

create table atest_aa_erp_kt_teklifler_backup
(
    id                       bigint identity (100000, 11),
    X_FIRSAT_NO              nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    TEKLIF_NO                nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    YARATILIS_TARIHI         date,
    YARATILIS_SAATI          time,
    TEKLIF_TIPI              int,
    SATIS_TUTARI             money,
    KILIT                    int,
    SATIS_TIPI               nvarchar(3) collate SQL_Latin1_General_CP1254_CI_AS,
    VADE                     nvarchar(5) collate SQL_Latin1_General_CP1254_CI_AS,
    KOMISYON_F1              money,
    KOMISYON_F1_ACIKLAMA     text collate SQL_Latin1_General_CP1254_CI_AS,
    KOMISYON_F2              money,
    KOMISYON_F2_ACIKLAMA     text collate SQL_Latin1_General_CP1254_CI_AS,
    KOMISYON_H               money,
    KOMISYON_H_ACIKLAMA      text collate SQL_Latin1_General_CP1254_CI_AS,
    SEC                      int,
    SIL                      tinyint,
    NOTLAR                   text collate SQL_Latin1_General_CP1254_CI_AS,
    KAMPANYA                 text collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY1                    int,
    ONAY1_KIM                nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY1_ACIKLAMA           nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY2                    int,
    ONAY2_KIM                nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY2_ACIKLAMA           nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    VADE_ONAY                int,
    VADE_ONAY_ACIKLAMA       text collate SQL_Latin1_General_CP1254_CI_AS,
    VADE_ONAY_KIM            text collate SQL_Latin1_General_CP1254_CI_AS,
    TEKLIF_SURE              int,
    TEKLIF_EK_NOT            text collate SQL_Latin1_General_CP1254_CI_AS,
    TEKLIF_LISTELI           int,
    t_maliyet                money,
    t_satis                  money,
    KOMISYON_TIP1            nvarchar(16) collate SQL_Latin1_General_CP1254_CI_AS,
    KOMISYON_TIP2            nvarchar(16) collate SQL_Latin1_General_CP1254_CI_AS,
    PDF                      int,
    KOMISYON_ODENDI          int,
    KOMISYON_ODENDI_ACIKLAMA nvarchar(99) collate SQL_Latin1_General_CP1254_CI_AS,
    KOMTERA_HIZMET_BEDELI    money,
    KOMTERA_HIZMET_ADI       nvarchar(30) collate SQL_Latin1_General_CP1254_CI_AS,
    KOMISYON_F3              money,
    KOMISYON_F3_ACIKLAMA     text collate SQL_Latin1_General_CP1254_CI_AS,
    KILIT_TARIHI             date,
    yenileme_log             nvarchar(30) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_teklifler_onay
(
    id                 bigint identity (1, 11)
        constraint [PK_[atest_aa_erp_kt_teklifler_onay]
            primary key,
    TEKLIF_NO          nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY1              int,
    ONAY1_KIM          text collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY1_ACIKLAMA     text collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY2              int,
    ONAY2_KIM          text collate SQL_Latin1_General_CP1254_CI_AS,
    ONAY2_ACIKLAMA     text collate SQL_Latin1_General_CP1254_CI_AS,
    VADE_ONAY          int,
    VADE_ONAY_ACIKLAMA text collate SQL_Latin1_General_CP1254_CI_AS,
    VADE_ONAY_KIM      text collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_teklifler_urunler
(
    id               bigint identity
        constraint PK_atest_aa_erp_kt_teklifler_urunler
            primary key,
    X_TEKLIF_NO      nvarchar(10) collate SQL_Latin1_General_CP1254_CI_AS,
    SIRA             int
        constraint DF__atest_aa_erp_kt___SIRA__78634656 default 1,
    YARATILIS_TARIHI date
        constraint DF_atest_aa_erp_kt_teklifler_urunler_YARATILIS_TARIHI default getdate(),
    SKU              nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    ACIKLAMA         text collate SQL_Latin1_General_CP1254_CI_AS,
    TIP              nvarchar(35) collate SQL_Latin1_General_CP1254_CI_AS,
    SURE             int,
    ADET             int
        constraint DF__atest_aa_erp_kt___ADET__7A4B8EC8 default 1,
    B_LISTE_FIYATI   money
        constraint DF__atest_aa_erp_kt__B_LIS__7B3FB301 default 0,
    O_MALIYET        money
        constraint DF__atest_aa_erp_kt__O_MAL__7C33D73A default 0,
    B_MALIYET        money
        constraint DF__atest_aa_erp_kt__B_MAL__7D27FB73 default 0,
    ISKONTO          float,
    B_SATIS_FIYATI   money
        constraint DF__atest_aa_erp_kt__B_SAT__7E1C1FAC default 0,
    SEC              int,
    SATIS_TIPI       int default 0,
    slot             int,
    MEVCUT_LISANS    nvarchar(35) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create index atest_aa_erp_kt_teklifler_urunler_SKU_IDX
    on atest_aa_erp_kt_teklifler_urunler (SKU)
    go

create index IDX_X_TEKLIF_NO
    on atest_aa_erp_kt_teklifler_urunler (X_TEKLIF_NO)
    go

create index IDX_SKU
    on atest_aa_erp_kt_teklifler_urunler (SKU)
    go

create index idx_teklifno_sku
    on atest_aa_erp_kt_teklifler_urunler (X_TEKLIF_NO, SKU)
    go

create index IX_atest_aa_erp_kt_teklifler_urunler_X_TEKLIF_NO_TIP
    on atest_aa_erp_kt_teklifler_urunler (X_TEKLIF_NO, TIP) include (B_SATIS_FIYATI, ADET)
go

create table atest_aa_erp_kt_tl_fatura_marka
(
    id    int identity
        constraint PK_atest_aa_erp_kt_tl_fatura_marka
            primary key,
    marka nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_users
(
    id             int identity
        primary key,
    kullanici      nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS not null,
    sifre          nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS not null,
    ePosta         nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS not null,
    adiSoyadi      nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS,
    telefon        nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    del            int default 0,
    admin          int default 0,
    super_admin    int default 0,
    pasif          int default 0,
    LOGO_kullanici nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS,
    markalar       text collate SQL_Latin1_General_CP1254_CI_AS,
    departman      nvarchar(255) collate SQL_Latin1_General_CP1254_CI_AS,
    cinsiyet       nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kt_vatan_faturalama
(
    id          int identity
        constraint PK_atest_aa_erp_kt_vatan_faturalama
            primary key,
    magaza      nvarchar(150) collate SQL_Latin1_General_CP1254_CI_AS,
    adres       text collate SQL_Latin1_General_CP1254_CI_AS,
    sehir       text collate SQL_Latin1_General_CP1254_CI_AS,
    sku         text collate SQL_Latin1_General_CP1254_CI_AS,
    doviz_turu  nvarchar(3) collate SQL_Latin1_General_CP1254_CI_AS,
    adet        int,
    birim_fiyat money,
    belge_no    nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_kur
(
    tarih date
        constraint DF_atest_aa_erp_kur_date default getdate() not null
        constraint PK_atest_aa_erp_kur
            primary key,
    usd   money,
    eur   money
)
    go

create index IDX_TARIH
    on atest_aa_erp_kur (tarih desc)
    go

create table atest_aa_erp_kur_yk
(
    id    int identity
        constraint PK_atest_aa_erp_kur_yk
            primary key,
    tarih date
        constraint DF_atest_aa_erp_kur_yk_tarih default getdate(),
    usd   money,
    eur   money
)
    go

create table atest_aa_erp_log
(
    id        int identity
        constraint PK_atest_aa_erp_kt_log
            primary key,
    sirket    nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    tarih     datetime
        constraint DF_atest_aa_erp_kt_log_tarih default getdate(),
    kullanici nvarchar(20) collate SQL_Latin1_General_CP1254_CI_AS,
    modul     nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    islem_id  nvarchar(15) collate SQL_Latin1_General_CP1254_CI_AS,
    aciklama  nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    ip        text collate SQL_Latin1_General_CP1254_CI_AS
)
    go

create table atest_aa_erp_release_note
(
    id      int identity
        constraint PK_atest_aa_erp_release_note
            primary key,
    cd      datetime
        constraint DF_atest_aa_erp_release_note_cd default getdate(),
    company nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    text    text collate SQL_Latin1_General_CP1254_CI_AS,
    times   float
)
    go

create table atest_aa_erp_tickets
(
    id           int identity
        constraint PK_atest_aa_erp_tickets
            primary key,
    cd           date
        constraint DF_atest_aa_erp_tickets_cd default getdate(),
    cn           nvarchar(25) collate SQL_Latin1_General_CP1254_CI_AS,
    company      nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    modul        nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    type         nvarchar(15)
        constraint DF_atest_aa_erp_tickets_tip default N'Task' collate SQL_Latin1_General_CP1254_CI_AS collate SQL_Latin1_General_CP1254_CI_AS,
    priority     nvarchar(20)
        constraint DF_atest_aa_erp_tickets_aciliyet default N'Normal' collate SQL_Latin1_General_CP1254_CI_AS collate SQL_Latin1_General_CP1254_CI_AS,
    title        nvarchar(500) collate SQL_Latin1_General_CP1254_CI_AS,
    description  nvarchar(1500) collate SQL_Latin1_General_CP1254_CI_AS,
    comments     nvarchar(4000) collate SQL_Latin1_General_CP1254_CI_AS,
    dead_line    date,
    cc           text collate SQL_Latin1_General_CP1254_CI_AS,
    version      decimal(18, 1),
    status       nvarchar(11)
        constraint DF_atest_aa_erp_tickets_status default N'Open' collate SQL_Latin1_General_CP1254_CI_AS collate SQL_Latin1_General_CP1254_CI_AS,
    release_note text collate SQL_Latin1_General_CP1254_CI_AS,
    sure         float default '0'
)
    go

create table atest_aa_kt_logo_aktarim
(
    ID               int identity
        constraint PK_atest_aa_kt_logo_aktarim
            primary key,
    SIPARISID        int                                                   not null,
    NO               nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS not null,
    CARIKOD          nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS not null,
    MALZEMEKOD       nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS not null,
    BIRIM            nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS not null,
    MIKTAR           int                                                   not null,
    FIYAT            float                                                 not null,
    SATIS_TEMSILCISI nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS not null,
    SERI_NO          nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    FIS_DURUMU       int                                                   not null,
    SONUC            nvarchar(max) collate SQL_Latin1_General_CP1254_CI_AS not null,
    IRSALIYE_ID      int                                                   not null,
    SATIR_ID         int                                                   not null,
    FATURA_ID        int                                                   not null,
    IPTAL            int                                                   not null,
    BELGE_NO         nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    OZEL_KOD         nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    YETKI_KOD        nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    PROJE_KOD        nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    MESAJ            nvarchar(max) collate SQL_Latin1_General_CP1254_CI_AS,
    LG_IPTAL         int,
    CreateDate       nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS  not null,
    Cari_Vade_Kodu   nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    Sevk_Adresi      nvarchar(max) collate SQL_Latin1_General_CP1254_CI_AS,
    DOVIZKUR         float,
    DOVIZ_TUR        bigint,
    SevkiyatKime     nvarchar(7) collate SQL_Latin1_General_CP1254_CI_AS,
    Unvan            nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    KisiBilgi        nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    Adres1           nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    Adres2           nvarchar(50) collate SQL_Latin1_General_CP1254_CI_AS,
    IrsaliyeTarihi   datetime,
    MusteriSiparisNo nchar(30) collate SQL_Latin1_General_CP1254_CI_AS,
    BayiMusteri      nvarchar(250) collate SQL_Latin1_General_CP1254_CI_AS,
    Hizmetmi         tinyint,
    LisansSuresi     nvarchar(30) collate SQL_Latin1_General_CP1254_CI_AS,
    Ambar            int
)
    go

create table atest_aa_kt_pref_list
(
    id           varchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    tablo        nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    kullanici    nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS,
    parametreler nvarchar(100) collate SQL_Latin1_General_CP1254_CI_AS
)
    go

