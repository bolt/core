Feature: Get content with API

  Scenario: As a user I fetch all contents
    When I send a GET request to "/api/contents.json"
    Then the response status code should be 200
    And the response should be in JSON
    And the response should contain json:
  """
  [
    {
       "id": "@integer@",
       "contentType": "@string@",
       "publishedAt": "@string@.isDateTime()",
       "taxonomies": @array@,
       "authorName": "@string@",
       "fieldValues": {
          "title": "@string@",
          "slug": "@string@",
          "image": {
             "filename": "@string@",
             "alt": "@string@",
             "path": "@string@"
          },
          "@*@": "@*@"
       },
       "@*@": "@*@"
    },
    "@...@"
  ]
  """

  Scenario: As a user I fetch single content
    When I send a GET request to "/api/contents/1.json"
    Then the response status code should be 200
    And the response should be in JSON
    And the response should contain json:
  """
  {
     "id": 1,
     "contentType": "@string@",
     "publishedAt": "@string@.isDateTime()",
     "taxonomies": @array@,
     "authorName": "@string@",
     "fieldValues": {
        "title": "@string@",
        "slug": "@string@",
        "image": {
           "filename": "@string@",
           "alt": "@string@",
           "path": "@string@"
        },
       "@*@": "@*@"
     },
     "@*@": "@*@"
  }
  """

  Scenario: As a user I fetch homepage content in JSON+LD format
    When I send a GET request to "/api/contents.jsonld?contentType=homepage"
    Then the response status code should be 200
    And the response should be in JSON
    And the response should contain json:
  """
  {
     "@context": "/api/contexts/Content",
     "@id": "/api/contents",
     "@type": "hydra:Collection",
     "hydra:member": [
        {
           "id": "@integer@",
           "contentType": "homepage",
           "publishedAt": "@string@.isDateTime()",
           "taxonomies": @array@,
           "authorName": "@string@",
           "fieldValues": {
              "title": "@string@",
              "slug": "@string@",
              "image": {
                 "filename": "@string@",
                 "alt": "@string@",
                 "path": "@string@"
              },
              "@*@": "@*@"
           },
           "@*@": "@*@"
        }
     ],
     "hydra:totalItems": 1,
     "hydra:view": {
        "@id": @string@,
        "@type": "hydra:PartialCollectionView"
     },
     "hydra:search": {
        "@type": "hydra:IriTemplate",
        "hydra:template": @string@,
        "hydra:variableRepresentation": "BasicRepresentation",
        "hydra:mapping": @array@
     }
  }
  """