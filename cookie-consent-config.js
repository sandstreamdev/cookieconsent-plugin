const { message, privacyPolicyLink, cookiePolicyLink, name, domain, gaId, gtmId } = cookieConsentSettings;

const CookieConsent = window.CookieConsent;
const cc = new CookieConsent({
  position: "bottom",
  type: "opt-in",
  consentSettingsElementId: "btn-revokeChoice",
  layout: "categories",
  showCategories: {
    [CookieConsent.UNCATEGORIZED]: false,
    [CookieConsent.ESSENTIAL]: true,
    [CookieConsent.PERSONALIZATION]: false,
    [CookieConsent.ANALYTICS]: true,
    [CookieConsent.MARKETING]: false,
  },
  content: {
    message,
    privacyPolicyLink,
    cookiePolicyLink,
  },
  cookie: {
    domain,
    name,
  },
});

cc.on("initialized", function () {
  const { consents } = cc;

  if (consents[CookieConsent.ESSENTIAL] !== CookieConsent.ALLOW) {
    cc.open();
  }

  if (consents[CookieConsent.ANALYTICS] === CookieConsent.ALLOW) {
    initializeGTM();
  }
});

cc.on("popupClosed", function () {
  const { consents } = cc;

  if (consents[CookieConsent.ANALYTICS] === CookieConsent.ALLOW) {
    initializeGTM();
  } else {
    removeAnalyticsCookies();

    if (isGTMInitialized()) {
      location.reload();
    }
  }
});

function isGTMInitialized() {
  return window.gtmInitialized;
}

function initializeGTM() {
  if (!isGTMInitialized()) {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      "gtm.start": new Date().getTime(),
      event: "gtm.js",
    });
    var f = document.getElementsByTagName("script")[0];
    var j = document.createElement("script");
    j.async = true;
    j.src = `https://www.googletagmanager.com/gtm.js?id=${gtmId}`;
    f.parentNode.insertBefore(j, f);
    window.gtmInitialized = true;
  }
}

function removeAnalyticsCookies() {
  cc.deleteCookie("_ga");
  cc.deleteCookie("_gid");
  cc.deleteCookie(`_gat_${gaId}`);
}
