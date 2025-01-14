import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Apirone API PHP",
  description: "PHP library for working with the Apirone API",
  base: '/apirone-api-php/',
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    outline: 'deep',
    logo: '/logo-primarySmall.svg',
    nav: nav(),

    sidebar: sidebar(),

    socialLinks: [
      { icon: 'github', link: 'https://github.com/Apirone/apirone-api-php' }
    ],
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Since 2017 Apirone OÜ. All Rights Reserved.'
    },
    lastUpdated: {
      text: 'Updated at',
      formatOptions: {
        dateStyle: 'short',
        timeStyle: 'medium'
      }
    }
  }
})

function nav() {
  return  [
      { text: 'Home', link: '/' },
      { text: 'API Docs', link: 'https://apirone.com/docs' },
      {
        text: 'Ecosystem',
        items: [
          { text: 'FAQ', link: 'https://apirone.com/faq' },
          { text: 'Blog', link: 'https://apirone.com/blog' },
          { text: 'How to', link: 'https://apirone.com/how-to' },
          { text: 'Testing Bench', link: 'https://examples.apirone.com' },
        ]
      }
    ]
}

function sidebar() {
  return  [
      {
        text: 'API Methods',
        collapsed: false,
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
    ]
}