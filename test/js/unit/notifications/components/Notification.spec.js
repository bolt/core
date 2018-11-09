import { mount } from '@vue/test-utils'
import Notification from '@/notifications/Components/Notification'

describe('Notification', () => {
    it('is a Vue instance', () => {
        const wrapper = mount(Notification, {
            propsData: {
                message: "Test-message",
                type: "danger",
                closingLabel: "close",
                duration: "300"
            }
        })
        expect(wrapper.isVueInstance()).toBeTruthy()
    })
    it('has a name called "notification"', () => {
        const wrapper = mount(Notification, {
            propsData: {
                message: "Test-message",
                type: "danger",
                closingLabel: "close",
                duration: "300"
            }
        })
        expect(wrapper.vm.$options.name).toBe('bolt-notification')
    })
})
