<template>
  <div>
    <div
        class="block link"
        v-for="link in links"
        :key="link.id"
        >
            <a
                class="link__caller"
                :href="link.url"
                target="_blank"
                :title="link.title">
                {{ link.title }}
            </a>
            <div class="tooltip">
                <button class="link__buttons edit hover:text-green-400 font-bold" @click.prevent.stop="$emit('edit', link)"> Edit</button>
                <button class="link__buttons delete hover:text-red-400 font-bold"  @click.prevent.stop="deleteLink(link)"> Delete</button>
            </div>
      </div>
  </div>
</template>

<script>
export default {
    props: {
        links: {
            type: Array,
            default() {
                return []
            }
        }
    },

    methods: {
        deleteLink(link) {
            this.showConfirm({
                title: `Delete ${link.title} link`,
                content: "Are you sure you want to delete this link?",
                confirmationButtonText: "Yes, delete",
                confirm: () => {
                    axios.delete(`links/${link.id}`).then(() => {
                        this.$inertia.reload({
                            only: ['links'],
                            preserveState: true
                        })
                    })
                }
            })
        }
    }
}
</script>

<style lang="scss">
.link {
    font-weight: bold;
    margin: 5px;
    display: inline-block;
    transition: all ease .3s;
    position: relative;

    &__caller {
        border-bottom: 1px dashed dodgerblue;
    }

    &__buttons {
        margin: 0 5px;
    }

    .tooltip {
        position: absolute;
        display: flex;
        justify-content: space-around;
        top: -30px;
        background: rgba($color: #000000, $alpha: .7);
        display: none;
        transition: all ease .3s;
        color: white;
        padding: 5px 15px;
        border-radius: 8px;
    }

    &:hover {
        color: dodgerblue;

        .tooltip {
            display: flex;
        }
    }

}
</style>
