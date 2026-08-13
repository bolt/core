⚠️ PLEASE DON'T DISCLOSE SECURITY-RELATED ISSUES PUBLICLY, SEE BELOW.

If you have found a security issue in Bolt, please use the private vulnerability reporting  [Github](https://github.com/bolt/core/security/advisories/new) offers and don't disclose it publicly until we can provide a fix for it. If you wish, we'll credit you for finding verified issues when we release the patched version and Github advisory.

## A note on AI findings

AI is a great tool to help find vulnerabilities, but it is not perfect. Please verify any findings before reporting them. Make sure to also check our [AI policy](AI_POLICY.md).

If you are using AI to find vulnerabilities, please try to also submit a fix using your tokens. While we might not use it directly, it helps us to understand your report.

## A note on "Self XSS"

Bolt is a CMS, that allows users to edit content on a website. As such, all _authenticated users_ can:

- Edit content, and (depending on the field types) insert HTML and CSS in that content, with a variety of allowed attributes.
- Depending on the user level: Edit template files, and insert HTML, CSS and javascript in those.
- When explicitly configured for a content field type, an editor can store a raw Twig template. This template is run in an unsandboxed environment.
- Upload files to the site, which will become publicly available. In the default settings, this includes `.PDF` and   `.SVG` files.

We see these functionalities as _features_, and not as security issues. Please report the mentioned items only if they can be performed by non-authorized users, or other exploitable vulnerabilities.

## A note on Symfony/configuration vulnerabilities

Bolt is not meant to run directly without knowledge. Certain settings might not be secure by default and could lead to vulnerabilities when not properly configured when the project is consumed. We do accept suggestions to approve the default values when possible, but we will not create an advisory for this category.

## Disclaimer

Maintainers have the right to reject a report without providing an explanation: this includes reports that have been made only with AI.

Thank you for reading this policy, and happy vulnerability hunting!
