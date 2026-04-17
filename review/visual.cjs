// Uso: node review/visual.cjs --base=http://localhost:8000 --sites=desculpometro,botao
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const args = Object.fromEntries(
    process.argv.slice(2).map(a => {
        const [k, v] = a.replace(/^--/, '').split('=');
        return [k, v];
    })
);

const base  = args.base  || 'http://localhost:8000';
const sites = (args.sites || '').split(',').filter(Boolean);
const outDir = path.join(__dirname, '..', 'storage', 'app', 'screenshots');
fs.mkdirSync(outDir, { recursive: true });

(async () => {
    const browser = await chromium.launch();

    for (const slug of sites) {
        const url = slug === '_hub' ? base : `${base}/${slug}`;
        console.log(`  ${url}`);

        for (const vp of [
            { name: 'mobile',  width: 375,  height: 812 },
            { name: 'desktop', width: 1280, height: 800 },
        ]) {
            const ctx = await browser.newContext({
                viewport: { width: vp.width, height: vp.height },
            });
            const page = await ctx.newPage();

            try {
                await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 });

                // Verificar overflow horizontal
                const hasOverflow = await page.evaluate(() => {
                    return document.documentElement.scrollWidth > document.documentElement.clientWidth;
                });
                if (hasOverflow) console.log(`  overflow-x em ${slug} @ ${vp.name}`);

                // Verificar font-size do heading principal
                const headingSize = await page.evaluate(() => {
                    const h = document.querySelector('h1, h2');
                    return h ? parseFloat(getComputedStyle(h).fontSize) : null;
                });
                const min = vp.name === 'mobile' ? 32 : 48;
                if (headingSize !== null && headingSize < min) {
                    console.log(`  heading pequeno em ${slug} @ ${vp.name}: ${headingSize}px (min ${min}px)`);
                }

                await page.screenshot({
                    path: path.join(outDir, `${slug}-${vp.name}.png`),
                    fullPage: true,
                });
            } catch (e) {
                console.log(`  ERRO ${slug} @ ${vp.name}: ${e.message}`);
            }
            await ctx.close();
        }
    }

    await browser.close();
})();
