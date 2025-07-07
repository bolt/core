# Upgrade to 5.2.3

In file `config/packages/security.yaml` replace

```yaml
            - { path: '^%bolt.backend_url%', roles: IS_AUTHENTICATED_REMEMBERED }
            - { path: '^/(%app_locales%)%bolt.backend_url%', roles: IS_AUTHENTICATED_REMEMBERED }
```

By
```yaml
            - { path: '^%bolt.backend_url%($|/)', roles: IS_AUTHENTICATED_REMEMBERED }
            - { path: '^/(%app_locales%)%bolt.backend_url%($|/)', roles: IS_AUTHENTICATED_REMEMBERED }
```
