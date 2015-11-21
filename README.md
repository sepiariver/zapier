Zapier
======

### Integrate MODX with hundreds of apps and services.

Use cases include, just for example:

- Send form submissions from your website to any of the CRMs supported in Zapier.
- Notify your team via Slack or any other communication tool supported in Zapier, when a lead-gen form on your site is submitted.
- Notify your team when a new blog post is created.
- Post to social media when a new MODX Resource is published.
- When a user fills out a review on your site, if the rating is under a certain amount, send it to your support team in Zendesk, but if the rating is high send it to the marketing team.
- Literally countless other uses...

For more information, browse the [wiki on Github](https://github.com/sepiariver/zapier/wiki)

## Installation

Install Zapier via the Extras Installer in your MODX Manager. You can also download it directly from the [Extras repo](http://modx.com/extras/package/zapier) or the [Github repo](https://github.com/sepiariver/zapier/)

## Authentication

The first thing you will need to do is authenticate your Zapier account, to access your MODX data and subscribe to the services you enable. The "gold standard" for doing this is OAuth2, which is a tad complicated to setup. Luckily there's a MODX Extra that handles this for you with ease. Install the [OAuth2Server Extra](http://modx.com/extras/package/oauth2server), or learn more at the [Github repo](https://github.com/modxcms/oauth2-server).

### Your Zapier App

The authentication settings in your Zapier app need to be configured with the URLs for your OAuth2Server endpoints. You also need to create a Client ID and Client Secret (the OAuth2Server Extra makes this a button-click affair). 

Setting up your Zapier app (in the Zapier dashboard) is beyond the scope of this overview, but you can find some guidance [here](https://github.com/sepiariver/zapier/wiki/Inside-Zapier). If you have a subscription to [MODX Cloud](https://modxcloud.com/), submit a support request from the MODX Cloud Dashboard with the subject "Zapier App Template Request" and I'll invite you to my Zapier app, which has placeholders to enter your auth info and will save you a couple of hours.

![Pre-made Zapier App](https://www.dropbox.com/s/o7uwnir1gneleyw/Screenshot%202015-11-20%2015.09.14.png?dl=1)

## Usage

### Quick Overview

1. After setting up your Zapier app, you will be able to add a connection from the Zapier dashboard. This is the step where you authorize Zapier. 
![Zapier Add Connection](https://www.dropbox.com/s/4sxdi08fco5vzio/Screenshot%202015-11-20%2015.03.01.png?dl=1)
2. The OAuth2Server Extra will send Zapier an authorization code, which Zapier can exchange for an access token. All further requests to your MODX site will be accompanied by this access token as a request parameter. 
3. Once successfully connected, you can start adding triggers. As of version 0.7.x there are four available MODX triggers: 2 for form submissions and 2 for MODX Resources. 
![MODX Triggers for Zapier](https://www.dropbox.com/s/ftfhp7kxqgu18ia/Screenshot%202015-11-20%2015.05.12.png?dl=1)

The Zapier Extra installs with 5 Snippets and 1 Plugin. 

_IMPORTANT: these Snippets expose data from your website. It's strongly recommended to always call the [[!verifyOAuth2]] Snippet in your Resource/Template, before calling one of these Snippets, to ensure all requests are authorized._

### zapierAddSubscription / zapierRemoveSubscription

These allow Zapier to "subscribe" to services from your MODX site. Zapier provides a target URL for each subscription. The Zapier Extra in MODX is responsible for storing these target URLs, and the events on which to send a payload to each. You can manually remove a subscription using the Zapier Extra's Manager page (CMP) but generally your actions in the Zapier dashboard will result in the creation and deletion of subscriptions as needed.

![Zapier Extra CMP in MODX](https://www.dropbox.com/s/2s96d2b4z2zksli/Screenshot%202015-11-20%2015.07.44.png?dl=1)

These two Snippets must be called in published MODX Resources, the URLs for which need to be entered into the Trigger Settings in your Zapier app. Upon installation, the Zapier Extra attempts to create these 2 Resources for you.

![Zapier Extra Subscription Endpoints](https://www.dropbox.com/s/cl8qhqapssvq1n9/Screenshot%202015-11-20%2015.10.44.png?dl=1)

### zapierSendFormToSubscribers / zapierPollSavedForms

"zapierSendFormToSubscribers" is a FormIt hook. Upon form submission, it queries the ZapierSubscriptions table for target URLs for the "new_form" event. (The event name can be customized via Snippet properties if need be.) It will attempt to send serialized form data to each of those target URLs. Depending on the response from Zapier, it will either assume success and try the next target URL or it will remove the subscription, because it has become invalid. There are a variety of reasons this might be the case, but suffice to say that Zapier strongly suggests removing unwanted subscriptions.

"zapierPollSavedForms" will return a JSON response listing saved form submissions. Forms can be saved using the "FormItSaveForm" hook that comes with FormIt (as of version 2.2.2). Calling this Snippet in a Resource creates a "polling" endpoint where Zapier can request data at any time. However this has the effect of increasing your server load, because Zapier is unaware of whether there is new data, and simply "polls" your site on an interval. The preferred usage is with a subscription, and the "zapierSendFormToSubscribers" hook.

_NOTES WHEN USING SUBSCRIPTIONS:_

- Even if you're using the hook to send data live, it's good practice to save the form submissions in case something goes wrong.
- Your Zapier app will require the polling Resource to be available as a "fallback". As such, the Zapier Extra attempts to install a sample form polling endpoint, which also contains an example call to the `[[!verifyOAuth2]]` Snippet, protecting your polling endpoint.
- The form polling endpoint serves a 2nd, important role: when setting up your trigger, the polling Resource provides sample data with which to map your fields to the consuming app, or "action" in Zapier. Since your forms can have arbitrary fields, it's vital that you setup the polling Resource, otherwise you won't have meaningful data with which to map. However, for ongoing data exchange, it's much preferred to use the subscription flow.

### ZapierSendResourceToSubscribers / zapierPollNewResources

"ZapierSendResourceToSubscribers" is a Plugin that fires when a Resource Edit form in the MODX Manager is saved. This could be when a Resource is created, updated, or both, depending on Plugin properties. "OnDocFormSave", the Plugin queries the ZapierSubscriptions table for target URLs for the event "resource_save" (the event name can be customized). As with the form sending Snippet, it may remove a subscription if sending the payload results in an error response.

"zapierPollNewResources" is a Snippet that, when called in a Resource, creates a "polling" endpoint for newly created Resources. All the downsides of polling for new form submissions, described above, apply here. It's highly recommended that you use the subscription flow, and let the Plugin do its thing.

As with forms, when using subscriptions, your Zapier app will require the polling Resource to be available as a "fallback". Thus, the Zapier Extra will attempt to install a sample Resource for you.

## Troubleshooting

If you're having problems, try to isolate the cause by asking a few questions:

At what step is it failing? Are you getting redirected to the form that asks you to authorize?

If not, check that you include the scheme "https:// before your URL. Using my app you don't do that, but when you setup your own app, you need to. 

Also the "response_type" URL parameter must be set to "code". I can't remember if I appended it to the authorization URL or if I added it as an auth field in my Zapier app, but if you're setting up your own, ensure that param is sent.

If you are getting the authorization form, and after clicking "yes" you get an error, check the OAuth2Server CMP to see if an access_token was in fact created.

If yes, your verification endpoint is failing. If no, the auth code delivery or the exchange for a token is failing.

If the latter is the case, check that your Client ID and Client Secret don't have a trailing space character. Copy/pasting from the CMP can do this, for some unknown and aggravating reason.

If your verification is failing, check that your app sends the access_token as a query parameter and NOT in the header. Token in header is not supported yet.

## Roadmap

The [milestones](https://github.com/sepiariver/zapier/milestones) in the Github repo describe the intended direction for this Extra, although submitted [issues](https://github.com/sepiariver/zapier/issues) and [PRs](https://github.com/sepiariver/zapier/pulls) may change that.
