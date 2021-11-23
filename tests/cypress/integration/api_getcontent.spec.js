/// <reference types="cypress" />

describe('As a user I want to fetch all contents of an API' , () => {
    it('Checks that GET response equals 200', () => {
        cy.login();
        cy.visit('/bolt/api');
        cy.get('#operations-Content-getContentCollection').eq(0).click();
        cy.get('.response-col_status').should('contain', '200');
    })

    it('Checks if the contents.json is filled with all content', () => {
        cy.login();
        cy.request({ 
            url: '/api/contents.json', 
            failOnStatusCode: false,
            auth: {
                username: 'admin',
                password: 'admin%1',
            },
        }).then((response) => {
            return new Promise(resolve => {        
                expect(response).property('status').to.eq(200)
                expect(response.body[0]).property('id').to.not.be.oneOf([null, ""])
                const respBody = response.body[0];
                const fieldId = respBody;
                resolve(fieldId)
            });
        })
    })

    it('Check if it returns JSON of a single record', () => {
        cy.login();
        cy.request({ 
            url: '/api/contents/1.json', 
            failOnStatusCode: false,
            auth: {
                username: 'admin',
                password: 'admin%1',
            },
        }).then((response) => {
            return new Promise(resolve => {        
                expect(response).property('status').to.eq(200)
                expect(response.body).property('id').to.not.be.oneOf([null, ""])
                const respBody = response.body[0];
                const fieldId = respBody;
                resolve(fieldId)
            });
        })
    })
    
    it('Check if the JSON LD format is working', () => {
        cy.login();
        cy.request({ 
            url: '/api/contents.jsonld', 
            failOnStatusCode: false,
            auth: {
                username: 'admin',
                password: 'admin%1',
            },
        }).then((response) => {
            return new Promise(resolve => {        
                expect(response).property('status').to.eq(200)
                expect(response.body).property('hydra:totalItems').to.not.be.oneOf([null, "", 0])
                const respBody = response.body;
                const fieldId = respBody;
                resolve(fieldId)
            });
        })
    })
    //TODO fix this test once we can navigate inside object
    it('Check if the JSON LD format is working for single contenttypes like homepage', () => {
        cy.login();
        cy.request({ 
            url: '/api/contents.jsonld?contentType=homepage', 
            failOnStatusCode: false,
            auth: {
                username: 'admin',
                password: 'admin%1',
            },
        }).then((response) => {
            return new Promise(resolve => {        
                expect(response).property('status').to.eq(200)
                expect(response.body).property('hydra:totalItems').to.not.be.oneOf([null, "", 0])
                const respBody = response.body;
                const fieldId = respBody;
                resolve(fieldId)
            });
        })
    })

    it('Check if the JSON LD format is working for single records', () => {
        cy.login();
        cy.request({ 
            url: '/api/contents/1.jsonld', 
            failOnStatusCode: false,
            auth: {
                username: 'admin',
                password: 'admin%1',
            },
        }).then((response) => {
            return new Promise(resolve => {        
                expect(response).property('status').to.eq(200)
                expect(response.body).property('id').to.not.be.oneOf([null, ""])
                const respBody = response.body;
                const fieldId = respBody;
                resolve(fieldId)
            });
        })
    })
})

describe('Test reading JSON Fields', () => {    
    it('should read the values of the returned data in JSON', () => {
        cy.request({
        url:`/api/contents/1/fields.json`, 
        failOnStatusCode: false,
        auth: {
            username: 'admin',
            password: 'admin%1',
        },
        }).then((response) => {
            return new Promise(resolve => {        
                expect(response).property('status').to.eq(200)
                expect(response.body[0]).property('name').to.not.be.oneOf([null, ""])
                const respBody = response.body[0];
                const fieldId = respBody;
                resolve(fieldId)
            });
        })
    })

    it('should read the values of the returned data in JSON ld', () => {
        cy.request({
        url:`/api/contents/1/fields.jsonld`, 
        failOnStatusCode: false,
        auth: {
            username: 'admin',
            password: 'admin%1',
        },
        }).then((response) => {
            return new Promise(resolve => {        
                expect(response).property('status').to.eq(200)
                expect(response.body).property('hydra:totalItems').to.not.be.oneOf([null, "", 0])
                const respBody = response.body;
                const fieldId = respBody;
                resolve(fieldId)
            });
        })
    })
})
