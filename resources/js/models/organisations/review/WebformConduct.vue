<template>
    <div class="modal fade col-12 full-width" id="webformConductModal" tabindex="-1" aria-labelledby="webformConductModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ collection?.messages?.webform }} {{ collection?.messages?.conduct }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="modalClose"></button>
                </div>
                <div class="row">
                    <!-- Components and Statements-->
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ collection?.messages?.webforms }}</h4>
                            </div>
                            <div class="list-group">
                                <a v-for="webform in webforms" :key="webform" href="#" class="list-group-item list-group-item-action active">Cras justo odio </a>
                                <a href="#" class="list-group-item list-group-item-action active">Cras justo odio </a>
                                <a href="#" class="list-group-item list-group-item-action">Dapibus ac facilisis in</a>
                                <a href="#" class="list-group-item list-group-item-action">Morbi leo risus</a>
                                <a href="#" class="list-group-item list-group-item-action">Porta ac consectetur ac</a>
                                <a href="#" class="list-group-item list-group-item-action disabled">Vestibulum at eros</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'WebformConduct',
    props: ["actionId", "collection", "locale", "org"],
    data() {
        return {
            webforms: [],
        }
    },
    methods: {
        modalClose() {
            $("#webformConductModal").modal("hide");
        },
        rebuild() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/review/" + thisComponent.actionId, {}) 
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.webforms = response.data.statistics?.statements?.webform?.webforms;
                    /*
                    if (thisComponent.interviews.length > 0) {
                        // do we have an expanded already
                        if (thisComponent.interviewExpanded) {
                            // find expanded from interviews
                            let ex = thisComponent.interviews.filter(i => {
                                return i.id == thisComponent.interviewExpanded.id
                            });
                            ex = ex[0];
                            thisComponent.interviewExpanded = ex;
                        } else {
                            thisComponent.interviewExpanded = thisComponent.interviews[0];
                        }
                    };
                    // clear inputs
                    const elements = document.querySelectorAll('[id^="interviewReviewText"]');
                    // Loop through the elements and set their value to null (empty string)
                    elements.forEach(element => {
                        element.value = '';
                    });
                    // rebuild sliders
                    thisComponent.interviewExpandedBuildSliders();
                    */
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
        },
        webformConduct() {
            this.rebuild();
            this.$nextTick(() => {
                console.log(this.collection);
                $("#webformConductModal").modal("show");
            })
        }
    }

}
</script>