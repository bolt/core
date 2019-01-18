module.exports = {
    "browserWidth": 1600,
    "browserHeight": 900,
    "timeout": 60,
    "intervalEmail": 5,
    "maxEmailRepeats": 5,
    "elementsVisibilityTimeout": 10,
    "waitForPageTimeout": 30,
    "downloadTimeout": 30,
    "emails": [
        "/emails"
    ],
    "reports": "../../var/log/e2e-reports",
    "downloads": "/downloads",
    "data": "/data",
    "features": [
        "/features"
    ],
    "pages": [
        "/pages"
    ],
    "matchers": [
        "/matchers"
    ],
    "generators": [
        "/generators"
    ],
    "form_handlers": [
        "/form_handlers"
    ],
    "step_definitions": [
        "/step_definitions"
    ],
    "comparators": [
        "/comparators"
    ],
    "dictionaries": [
        "/dictionaries"
    ],
    "transformers": [
        "/transformers"
    ],
    "regexes": [
        "/regexes"
    ],
    "hooks": [
        "/hooks"
    ],
    "clearEmailInboxBeforeTests": false,
    "clearCookiesAfterScenario": true,
    "clearLocalStorageAfterScenario": true,
    "email": null,
    "headless": true,
    "noGpu": false,
    "type": "otherWeb",
    "baseUrl": "http://127.0.0.1:8088",
    "accounts": {
        "admin": {
            "accounts": [
                {
                    "username": "admin",
                    "password": "admin%1"
                }
            ]
        }
    }
}
