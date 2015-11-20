Zapier
======

### Integrate MODX with hundreds of apps and services.

Use cases include, just for example:

- Send form submissions from your website to any of the CRMs supported in Zapier
- Notify your team via Slack or any other communication tool supported in Zapier, when a lead-gen form on your site is submitted
- Notify your team when a new blog post is created
- Post to social media when a new MODX Resource is published
- When a user fills out a review on your site, if the rating is under a certain amount, send it to your support team in Zendesk, but if the rating is high send it to the marketing team
- Literally countless other uses

## Installation

Install Zapier via the Extras Installer in your MODX Manager

## Authentication

The first thing you will need to do is authenticate your Zapier account, to access your MODX data and subscribe to the services you enable. The "gold standard" for doing this is OAuth2, which is a tad complicated to setup. Luckily there's a MODX Extra that handles this for you with ease. Install the OAuth2Server Extra via the Extras Installer, or learn more here.

### Your Zapier App

The authentication settings in your Zapier app need to be configured with the URLs for your OAuth2Server endpoints. You also need to create a Client ID and Client Secret (the OAuth2Server Extra makes this a button-click affair). Setting up your Zapier app is beyond the scope of this overview, but you can find some guidance [here]. If you have a subscription to MODX Cloud, submit a support request from the MODX Cloud Dashboard with the subject "Zapier App Template Request" and I'll invite you to my Zapier app, which has placeholders to enter your URLs.

## Usage

The Zapier Extra installs with 5 Snippets.

### zapierAddSubscription

This must be called in a published Resource, where Zapier can request subscriptions to the 





