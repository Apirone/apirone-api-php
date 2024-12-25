import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Apirone API PHP",
  description: "PHP library for working with the Apirone API",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    logo:[
      '/assets/logo-primarySmall.svg'
    ],
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Apirone API', link: 'https://apirone.com/docs' },
      {
        text: 'Dropdown Menu',
        items: [
          { text: 'Item A', link: '/item-1' },
          { text: 'Item B', link: '/item-2' },
          { text: 'Item C', link: '/item-3' }
        ]
      }
    ],

    sidebar: [
      {
        text: 'API Methods',
        items: [
          { text: 'Authorization', link: '/Authorization' },
          { text: 'Wallet', link: '/Wallet' },
          { text: 'Account', link: '/Account' },
          { text: 'Invoices', link: '/Invoices' },
          { text: 'Services', link: '/Services' },
          { text: 'Helpers', link: '/Helpers' },
          { text: 'Log handling', link: '/LogHandling' },
        ]
      },
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/Apirone/apirone-api-php' }
    ],
    outline: 'deep',
    lastUpdated: {
      text: 'Updated at',
      formatOptions: {
        dateStyle: 'short',
        timeStyle: 'medium'
      }
    }
  }
})
