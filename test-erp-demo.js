const { chromium } = require('playwright');

async function testERPDemo() {
    // Chrome tarayıcısını başlat
    const browser = await chromium.launch({ 
        headless: false, // Tarayıcıyı görünür modda aç
        slowMo: 1000 // İşlemler arası 1 saniye bekle
    });
    
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 }
    });
    
    const page = await context.newPage();
    
    try {
        console.log('ERP test sayfasına gidiliyor...');
        
        // ERP test sayfasına git
        await page.goto('http://erptest.komtera.com', { 
            waitUntil: 'networkidle',
            timeout: 30000 
        });
        
        console.log('Giriş sayfası yüklendi, giriş yapılıyor...');
        
        // Kullanıcı adı ve şifre girişi
        await page.fill('#user_login', 'gokhan.ilgit');
        await page.fill('#user_pass', 'asa');
        
        console.log('Giriş bilgileri girildi, giriş butonuna tıklanıyor...');
        
        // Giriş butonuna tıkla
        await page.click('#wp-submit');
        
        // Giriş sonrası sayfanın yüklenmesini bekle
        await page.waitForLoadState('networkidle');
        
        console.log('Giriş yapıldı! Dashboard ekran görüntüsü alınıyor...');
        
        // Giriş sonrası ekran görüntüsü
        await page.screenshot({ 
            path: 'erp-dashboard.png',
            fullPage: true 
        });
        
        console.log('Dashboard görüntüsü alındı: erp-dashboard.png');
        
        // Demolar linkini ara
        console.log('Demolar linki aranıyor...');
        
        // Farklı demo link seçeneklerini dene
        const demoSelectors = [
            'a:has-text("Demo")',
            'a:has-text("Demolar")',
            'a[href*="demo"]',
            'text=Demo',
            'text=Demolar'
        ];
        
        let demoFound = false;
        for (const selector of demoSelectors) {
            const demoLink = page.locator(selector);
            if (await demoLink.count() > 0) {
                console.log(`Demo linki bulundu (${selector}), tıklanıyor...`);
                await demoLink.first().click();
                await page.waitForLoadState('networkidle');
                demoFound = true;
                break;
            }
        }
        
        if (demoFound) {
            console.log('Demo sayfası açıldı! Ekran görüntüsü alınıyor...');
            await page.screenshot({ 
                path: 'erp-demo-sayfa.png',
                fullPage: true 
            });
            console.log('Demo sayfası görüntüsü alındı: erp-demo-sayfa.png');
        } else {
            console.log('Demo linki bulunamadı, sayfadaki menüler kontrol ediliyor...');
            
            // Sayfadaki tüm linkleri ve menüleri listele
            const links = await page.locator('a').all();
            console.log('Sayfada bulunan linkler:');
            
            for (let i = 0; i < Math.min(links.length, 15); i++) {
                const link = links[i];
                const text = await link.textContent();
                const href = await link.getAttribute('href');
                if (text && text.trim()) {
                    console.log(`- ${text.trim()} (${href || 'href yok'})`);
                }
            }
            
            // Menü içeriğini de kontrol et
            const menuItems = await page.locator('li, .menu-item, .wp-admin-bar-node').all();
            console.log('\nMenü öğeleri:');
            
            for (let i = 0; i < Math.min(menuItems.length, 10); i++) {
                const item = menuItems[i];
                const text = await item.textContent();
                if (text && text.trim() && text.length < 50) {
                    console.log(`- ${text.trim()}`);
                }
            }
        }
        
        // 5 saniye bekle (inceleme için)
        console.log('5 saniye bekleniyor...');
        await page.waitForTimeout(5000);
        
    } catch (error) {
        console.error('Hata oluştu:', error.message);
        
        // Hata durumunda da ekran görüntüsü al
        await page.screenshot({ 
            path: 'erp-hata.png',
            fullPage: true 
        });
        
        console.log('Hata ekran görüntüsü alındı: erp-hata.png');
    } finally {
        // Tarayıcıyı kapat
        await browser.close();
        console.log('Test tamamlandı!');
    }
}

// Scripti çalıştır
testERPDemo().catch(console.error);