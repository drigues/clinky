import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.data('shareBar', (text, url) => ({
    canShare: typeof navigator.share !== 'undefined',
    copied: false,
    track(platform) {
        const csrfToken = document.querySelector('meta[name=csrf-token]')
        if (!csrfToken) return
        fetch('/api/track', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
            },
            body: JSON.stringify({ event: 'share_' + platform })
        }).catch(() => {})
    },
    async nativeShare() {
        try {
            await navigator.share({ text, url, title: document.title })
            this.track('native')
        } catch(e) {}
    },
    copy() {
        navigator.clipboard.writeText(text + '\n\n' + url).then(() => {
            this.copied = true
            setTimeout(() => this.copied = false, 2000)
        })
        this.track('copy')
    }
}))

Alpine.start()
