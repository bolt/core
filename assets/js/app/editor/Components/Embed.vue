<template>
    <div>
        <!-- .field--embed -->
        <div :id="name" class="editor__embed" :name="name">
            <div class="row">
                <div class="col-8">
                    <div class="mb-4">
                        <label class="form-label" for="embed-url">{{ labels.content_url }}</label>
                        <div class="input-group">
                            <input
                                id="embed-url"
                                v-model="urlData"
                                class="form-control"
                                :name="name + '[url]'"
                                :placeholder="labels.placeholder_content_url"
                                :title="labels.placeholder_content_url"
                                type="url"
                                :required="required"
                                :readonly="readonly"
                                :data-errormessage="typeof errormessage === 'string' ? errormessage : undefined"
                                :pattern="typeof pattern === 'string' ? pattern : undefined"
                            />
                            <span class="input-group-append">
                                <button
                                    class="btn btn-tertiary refresh"
                                    type="button"
                                    :disabled="loading"
                                    :aria-label="labels.refresh"
                                    @click="updateEmbed"
                                >
                                    <i :class="(loading ? 'fa-spin' : '') + ' fas fa-sync me-0'"></i>
                                </button>

                                <button
                                    class="btn btn-hidden-danger remove"
                                    type="button"
                                    :aria-label="labels.delete"
                                    @click="clearEmbed"
                                >
                                    <i class="fas fa-trash me-0"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4 d-flex align-items-center">
                        <label class="form-label" for="embed-width-size">{{ labels.label_size }}</label>
                        <input
                            id="embed-width-size"
                            class="form-control w-auto col-2 offset-1"
                            :name="name + '[width]'"
                            type="number"
                            :value="widthData"
                            :readonly="readonly"
                            :title="labels.field_width"
                        />
                        ×
                        <label for="embed-height-size" class="sr-only">{{ labels.label_height }}</label>
                        <input
                            id="embed-height-size"
                            class="form-control w-auto col-2"
                            :name="name + '[height]'"
                            type="number"
                            :value="heightData"
                            :readonly="readonly"
                            :title="labels.field_height"
                        />
                        <span class="form-label">{{ labels.label_pixel }}</span>
                    </div>
                    <div class="mb-4">
                        <span class="form-label d-block">{{ labels.label_matched_embed }}</span>
                        <input
                            class="form-control title"
                            :name="name + '[title]'"
                            readonly
                            :aria-label="labels.field_title"
                            :title="labels.field_title"
                            type="text"
                            :value="titleData"
                        />
                        <input
                            class="form-control author_name"
                            :name="name + '[authorname]'"
                            readonly
                            :aria-label="labels.field_author"
                            :title="labels.field_author"
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
                    <span class="form-label d-block">{{ labels.label_preview }}</span>
                    <div class="editor__image--preview">
                        <a
                            v-if="previewImage !== null && previewImage !== ''"
                            class="editor__image--preview-image"
                            :href="previewImage"
                            :style="`background-image: url('${previewImage}')`"
                        >
                            <span class="sr-only">{{ labels.label_preview }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, onBeforeUnmount } from 'vue';
import baguetteBox from 'baguettebox.js';
import { debounce } from '../../utils/debounce';

const props = defineProps<{
    embedapi?: string;
    name?: string;
    authorurl?: string;
    authorname?: string;
    height?: number | string; //String if not set
    html?: string;
    thumbnail?: string;
    title?: string;
    url?: string;
    width?: number | string; //String if not set
    labels: Record<string, string>;
    required?: boolean;
    readonly?: boolean;
    errormessage?: string | boolean; //string if errormessage is set, and false otherwise
    pattern?: string | boolean;
}>();

const authorurlData = ref(props.authorurl);
const authornameData = ref(props.authorname);
const heightData = ref(props.height);
const htmlData = ref(props.html);
const thumbnailData = ref(props.thumbnail);
const titleData = ref(props.title);
const urlData = ref(props.url);
const widthData = ref(props.width);
const loading = ref(false);
const previewImage = ref(props.thumbnail);

const debouncedFetchEmbed = debounce(fetchEmbed, 500);

watch(urlData, () => {
    updateEmbed();
});

if (urlData.value) {
    updateEmbed();
}

watch(previewImage, initPreviewLightbox, { flush: 'post', immediate: true });

function initPreviewLightbox() {
    if (!previewImage.value) {
        return;
    }

    baguetteBox.run('.editor__image--preview', {});
}

onBeforeUnmount(() => {
    debouncedFetchEmbed.cancel();
});

function updateEmbed() {
    loading.value = true;
    debouncedFetchEmbed();
}

function clearEmbed() {
    urlData.value = '';
}

function fetchEmbed() {
    const body = new FormData();
    body.append('url', urlData.value ?? '');
    body.append('_csrf_token', (document.getElementsByName('_csrf_token')[0] as HTMLInputElement).value);

    fetch(props.embedapi as string, { method: 'POST', body: body })
        .then(response => response.json())
        .then(json => {
            authorurlData.value = json.author_url;
            authornameData.value = json.author_name;
            heightData.value = json.height;
            htmlData.value = json.html;
            thumbnailData.value = json.thumbnail_url;
            titleData.value = json.title;
            widthData.value = json.width;
            previewImage.value = json.thumbnail_url;
        })
        .catch(err => {
            console.warn(err);
        })
        .finally(() => {
            loading.value = false;
        });
}
</script>
