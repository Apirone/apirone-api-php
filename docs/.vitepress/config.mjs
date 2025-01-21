import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Apirone API PHP",
  description: "PHP library for working with the Apirone API",
  base: '/apirone-api-php/',
  head: [['link', { rel: 'icon', href: '/apirone-api-php/favicon.ico' }]],
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    outline: 'deep',
    logo: '/logo-primarySmall.svg',
    nav: nav(),

    sidebar: sidebar(),

    search: {
      provider: 'local'
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/Apirone/apirone-api-php' }
    ],
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2017-'+ getYear() + ' Apirone OÜ. All Rights Reserved.'
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
      { text: 'API Docs', link: 'https://apirone.com/docs' },
      {
        text: 'Helpful',
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
        items: [
          { text: 'Intro', link: '/Intro' },
          { text: 'Account', link: '/Account' },
          { text: 'Wallet', link: '/Wallet' },
          { text: 'Invoices', link: '/Invoices' },
          { text: 'Services', link: '/Services' },
          { text: 'Authorization', link: '/Authorization' },
          { text: 'Helpers', link: '/Helpers' },
          { text: 'Log handling', link: '/LogHandling' },
        ]
      },
    ]
}

function getYear() {
  return new Date().getFullYear()
}