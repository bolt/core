<template>
    <div>
        <!-- .field--embed -->
        <div :id="name" class="editor__embed" :name="name">
            <div class="row">
                <div class="col-8">
                    <div class="form-group">
                        <label for="embed-url">{{ labels.content_url }}</label>
                        <div class="input-group">
                            <input
                                v-model="urlData"
                                class="form-control"
                                :name="name + '[url]'"
                                :placeholder="labels.placeholder_content_url"
                                type="url"
                                :required="required"
                                :readonly="readonly"
                                :data-errormessage="errormessage"
                                :pattern="pattern"
                            />
                            <span class="input-group-append">
                                <button
                                    class="btn btn-tertiary refresh"
                                    type="button"
                                    :disabled="loading"
                                    @click="updateEmbed"
                                >
                                    <i :class="(loading ? 'fa-spin' : '') + ' fas fa-sync mr-0'"></i>
                                </button>

                                <button class="btn btn-hidden-danger remove" type="button" @click="clearEmbed">
                                    <i class="fas fa-trash mr-0"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group form-inline">
                        <label for="embed-width-size">{{ labels.label_size }}</label>
                        <input
                            class="form-control col-2 offset-1"
                            :name="name + '[width]'"
                            type="number"
                            :value="widthData"
                            :readonly="readonly"
                        />
                        ×
                        <label for="embed-height-size" class="sr-only">{{ labels.label_height }}</label>
                        <input
                            class="form-control col-2"
                            :name="name + '[height]'"
                            type="number"
                            :value="heightData"
                            :readonly="readonly"
                        />
                        <label>{{ labels.label_pixel }}</label>
                    </div>
                    <div class="form-group">
                        <label>{{ labels.label_matched_embed }}</label>
                        <input
                            class="form-control title"
                            :name="name + '[title]'"
                            readonly=""
                            title="Title"
                            type="text"
                            :value="titleData"
                        />
                        <input
                            class="form-control author_name"
                            :name="name + '[authorname]'"
                            readonly=""
                            title="Author"
                            type="text"
                            :value="authornameData"
                        />
                        <input class="author_url" :name="name + '[authorurl]'" type="hidden" :value="authorurlData" />
                        <input class="html" :name="name + '[html]'" type="hidden" :value="htmlData" />
                        <input
                            class="thumbnail_url"
                            :name="name + '[thumbnail]'"
                            type="hidden"
                            :value="thumbnailData"
                        />
                    </div>
                </div>
                <div class="col-4">
                    <label>{{ labels.label_preview }}</label>
                    <div class="editor__image--preview">
                        <a
                            v-if="previewImage !== null && previewImage !== ''"
                            class="editor__image--preview-image"
                            :href="previewImage"
                            :style="`background-image: url('${previewImage}')`"
                        >
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import _ from 'lodash';
import baguetteBox from 'baguettebox.js';
import field from '../mixins/value';

export default {
    name: 'EditorEmbed',
    mixins: [field],
    props: {
        embedapi: String,
        name: String,
        authorurl: String,
        authorname: String,
        height: Number | String, //String if not set
        html: String,
        thumbnail: String,
        title: String,
        url: String,
        width: Number | String, //String if not set
        labels: Object,
        required: Boolean,
        readonly: Boolean,
        errormessage: String | Boolean, //string if errormessage is set, and false otherwise
        pattern: String | Boolean,
    },
    data() {
        return {
            authorurlData: this.authorurl,
            authornameData: this.authorname,
            heightData: this.height,
            htmlData: this.html,
            thumbnailData: this.thumbnail,
            titleData: this.title,
            urlData: this.url,
            widthData: this.width,
            loading: false,
        };
    },
    watch: {
        urlData: function() {
            this.updateEmbed();
        },
    },
    mounted() {
        this.previewImage = this.thumbnail;
    },
    created: function() {
        this.debouncedFetchEmbed = _.debounce(this.fetchEmbed, 500);
        if (this.urlData) {
            this.updateEmbed();
        }
        this.previewImage = this.thumbnail;
    },
    updated() {
        baguetteBox.run('.editor__image--preview', {
            afterShow: () => {
                // noScroll.on();
            },
            afterHide: () => {
                // noScroll.off();
            },
        });
    },
    methods: {
        updateEmbed: function() {
            this.loading = true;
            this.debouncedFetchEmbed();
        },
        clearEmbed: function() {
            this.urlData = '';
        },
        fetchEmbed: function() {
            const body = new FormData();
            body.append('url', this.urlData);
            body.append('_csrf_token', document.getElementsByName('_csrf_token')[0].value);

            fetch(this.embedapi, { method: 'POST', body: body })
                .then(response => response.json())
                .then(json => {
                    this.authorurlData = json.author_url;
                    this.authornameData = json.author_name;
                    this.heightData = json.height;
                    this.htmlData = json.html;
                    this.thumbnailData = json.thumbnail_url;
                    this.titleData = json.title;
                    //this.url          = json.url;
                    this.widthData = json.width;
                    this.previewImage = json.thumbnail_url;
                })
                .catch(err => {
                    console.warn(err);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    },
};
</script>
