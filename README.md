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

The authentication settings in your Zapier app need to be configured with the URLs for your OAuth2Server endpoints. You also need to create a Client ID and Client Secret (the OAuth2Server Extra makes this a button-click affair). Setting up your Zapier app is beyond the scope of this overview, but you can find some guidance [here]. If you have a subscription to MODX Cloud, submit a support request from the MODX Cloud Dashboard with the subject "Zapier App Template Request" and I'll invite you to my Zapier app, which has placeholders to enter your auth info and will save you a couple of hours.

## Usage

### Quick Overview

1. After setting up your Zapier app, you will be able to add a connection from the Zapier dashboard. This is the step where you authorize Zapier.
2. The OAuth2Server Extra will send Zapier an authorization code, which Zapier can exchange for an access token. All further requests to your MODX site will be accompanied by this access token as a request parameter. 
3. Once successfully connected, you can start adding triggers. As of version 0.7.x there are four available MODX triggers: 2 for form submissions and 2 for MODX Resources.

The Zapier Extra installs with 5 Snippets and 1 Plugin.

### zapierAddSubscription / zapierRemoveSubscription

These allow Zapier to "subscribe" to services from your MODX site. Zapier provides a target URL for each subscription. The Zapier Extra in MODX is responsible for storing these target URLs, and the events on which to send a payload to each. You can manually remove a subscription using the Zapier Extra's Manager page (CMP) but generally your actions in the Zapier dashboard will result in the creation and deletion of subscriptions as needed.

These two Snippets must be called in published MODX Resources, the URLs for which need to be entered into the Trigger Settings in your Zapier app. Upon installation, the Zapier Extra attempts to create these 2 Resources for you.

### zapierSendFormToSubscribers / zapierPollSavedForms

The first of these 2 Snippets is a FormIt hook. Upon form submission, it queries the ZapierSubscriptions table for target URLs for the "new_form" event. (The event name can be customized via Snippet properties if need be.) It will attempt to send serialized form data to each of those target URLs. Depending on the response from Zapier, it will either move to the next target URL or it will remove the subscription, because it has become invalid. There are a variety of reasons this might be the case, but suffice to say that Zapier strongly suggests removing unwanted subscriptions.





