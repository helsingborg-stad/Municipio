# Mapping

To successfully configure an idp you need to comply to the attribute mapping found i Provider/YourProvider.php. If your provider is not present, you may add a provider configuration by a PR. 

## Azure AD
| Key                                                                                              | Values                                                                                                    |
|--------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------|
| `http://schemas.microsoft.com/identity/claims/tenantid`                                          | `12345678-1234-1234-1234-123456789abc`                                                                   |
| `http://schemas.microsoft.com/identity/claims/objectidentifier`                                 | `abcd1234-abcd-1234-abcd-12345678abcd`                                                                   |
| `http://schemas.microsoft.com/identity/claims/displayname`                                      | `John Doe - Example Corp`                                                                                |
| `http://schemas.microsoft.com/identity/claims/identityprovider`                                 | `https://sts.windows.net/12345678-1234-1234-1234-123456789abc/`                                          |
| `http://schemas.microsoft.com/claims/authnmethodsreferences`                                    | 1. `http://schemas.microsoft.com/ws/2008/06/identity/authenticationmethod/password` <br> 2. `http://schemas.microsoft.com/claims/multipleauthn` |
| `http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname`                               | `John`                                                                                                   |
| `http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname`                                 | `Doe`                                                                                                    |
| `http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress`                            | `john.doe@example.com`                                                                                   |
| `http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name`                                    | `john.doe@example.com`                                                                                   |
| `http://schemas.xmlsoap.org/ws/2005/05/identity/claims/companyname`                             | `Example Corporation`                                                                                    |
| `NameID`                                                                                        | `john.doe@example.com`                                                                                   |