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
                            <div class="list-group" style="margin-left: 5px !important">
                                <template v-for="component in components" :key="component.id">
                                    <a :class="componentClass(component)" style="font-weight: 800 !important">{{ component.text }}</a>
                                    <template v-for="statement in component.st" :key="statement.id">
                                        <div :class="statementClass(statement)" @click="statementActiveSet(statement)">
                                            <a style="padding-left: 28px !important" @click="statementActiveSet(statement)">{{ statement["content_" + locale].substring(0, 64) + "..." }}</a>
                                            <div class="d-flex justify-content-end align-items-center">
                                                <span class="badge badge-glow bg-secondary" style="margin-right: 5px !important">{{ statement.latestDeed?.value }}</span>
                                                <span :class="statementReviewClass(statement)">{{ statement.latestReview?.review_status }}</span>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>
                    <!-- Statement Details-->
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-2">{{ statementActive["content_" + locale] }}</h4>
                                <h5>{{ statementActive["desc_" + locale] }}</h5>
                            </div>
                        </div>
                        <!-- Statement Actions-->
                        <!-- Value Card-->
                        <div class="card">
                            <div class="card-header">
                                <h4 class="text-uppercase">{{ collection?.messages?.value }}</h4>
                            </div>
                            <div class="card-body">
                                <div :id="`webformStatementValueSlider${statementActive.id}`" class="mt-1 mb-3"></div>
                                <div class="mb-3">
                                    <label :for="`webformReviewText${statementActive.id}`" class="form-label">{{ collection?.messages?.review }}</label>
                                    <textarea class="form-control" :id="`webformReviewText${statementActive.id}`" :name="`webformReviewText${statementActive.id}`" rows="4"></textarea>
                                </div>
                                <!-- Value Action Buttons (Accept/Reject)-->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-success w-25 me-2" @click="reviewUpdate('accept')">
                                        {{ collection?.messages?.accept }}
                                    </button>
                                    <button type="button" class="btn btn-danger w-25" @click="reviewUpdate('reject')">
                                        {{ collection?.messages?.reject }}
                                    </button>
                                </div>
                                <!-- Last Updated -->
                                <p>{{ collection?.messages?.lastUpdated }}: {{ statementActive.latestDeed?.lastUpdated }}, {{ collection?.messages?.by }}: {{ statementActive.latestDeed?.user }}</p>
                                <!-- Last Review Comment -->
                                <p>{{ collection?.messages?.comment }}: {{ statementActive.latestDeed?.comment }}</p>
                                <!-- Latest Review Details-->
                                <p>{{ collection?.messages?.latestReview }}, {{ statementActive.latestReview?.user }}, {{ statementActive.latestReview?.lastUpdated }}: {{ statementActive.latestReview?.review }}</p>
                                <p :class="`text-${statementActive.latestReview?.class}`">{{ statementActive.latestReview?.review_status }}</p>
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
    name: "WebformConduct",
    props: ["actionId", "collection", "locale", "org"],
    data() {
        return {
            components: [],
            statementActive: {
                id: null,
                content_en: null,
                content_se: null,
            },
        };
    },
    methods: {
        buildSliders() {
            var thisComponent = this;
            thisComponent.$nextTick(() => {
                // create sliders for mounted interview statements
                let slider = document.getElementById(`webformStatementValueSlider${thisComponent.statementActive.id}`);
                if (slider.noUiSlider) {
                    slider.noUiSlider.destroy();
                }
                noUiSlider.create(slider, {
                    start: thisComponent.statementActive.latestDeed.value,
                    step: 1,
                    range: {
                        min: 1,
                        max: 5,
                    },
                    tooltips: true,
                    direction: "ltr",
                    pips: {
                        mode: "steps",
                        stepped: false,
                        density: 1,
                    },
                });
            });
        },
        componentClass(component) {
            let r = "list-group-item list-group-item-action";
            return r;
        },
        modalClose() {
            $("#webformConductModal").modal("hide");
        },
        next() {
            var thisComponent = this;
            // group all statements
            let group = [];
            thisComponent.components.forEach((c) => {
                c.st.forEach((s) => {
                    group.push(s);
                });
            });
            let index = group.findIndex((statement) => statement.id == thisComponent.statementActive.id);
            if (index == group.length - 1) {
                // do nothing
            } else {
                thisComponent.statementActive = group[index + 1];
            }
            thisComponent.$nextTick(() => {
                thisComponent.buildSliders();
            });
        },
        rebuild() {
            var thisComponent = this;
            document.getElementById(`webformReviewText${thisComponent.statementActive.id}`).value = null;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/review/" + thisComponent.actionId, {})
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.components = response.data.statistics?.statements?.webform?.components;
                    // set active statement
                    if (thisComponent.components.length > 0) {
                        let componentActive = thisComponent.components[0];
                        if (componentActive.st.length > 0 && thisComponent.statementActive.id == null) {
                            thisComponent.statementActive = componentActive.st[0];
                        }
                        // update current
                        if(thisComponent.statementActive.id != null && response.data.statistics?.statements?.webform?.components.length > 0) {
                            response.data.statistics?.statements?.webform?.components.forEach(c => {
                                c.st.forEach(s => {
                                    if(s.id == thisComponent.statementActive.id) {
                                        thisComponent.statementActive = s;
                                    }
                                })
                            })
                        }
                    }
                    
                    thisComponent.buildSliders();
                    /*
                    console.log(thisComponent.components);
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
        reviewUpdate(type) {
            var thisComponent = this;
            // init with an accept
            let reviewStatusId = 2;
            if (type == "accept") {
                reviewStatusId = 2;
            } else {
                reviewStatusId = 3;
            }
            // get the review text
            let rev = document.getElementById(`webformReviewText${thisComponent.statementActive.id}`).value;
            // get the updated value
            let sliderValue = document.getElementById(`webformStatementValueSlider${thisComponent.statementActive.id}`).noUiSlider.get();
            axios
                .post(`/${this.locale}/axios/organisations/${this.org}/review/conduct-update`, {
                    review_status_id: reviewStatusId,
                    review: rev,
                    statement_id: thisComponent.statementActive.id,
                    value: sliderValue,
                    deed_id: thisComponent.statementActive.latestDeed.id,
                })
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.rebuild();
                    toastr["success"]("👋 Updated!.", "Success", {
                        closeButton: true,
                        tapToDismiss: false,
                        progressBar: true,
                        rtl: false,
                    });
                    thisComponent.$nextTick(() => {
                        thisComponent.next();
                    });
                })
                .catch(function (error) {
                    //console.log(error);
                    //console.log(error.response);
                    toastr["error"](`👋 ${error.response?.data}`, "Error!", {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                    });
                });
        },
        statementActiveSet(statement) {
            var thisComponent = this;
            thisComponent.rebuild();
            thisComponent.$nextTick(() => {
                this.components.forEach((c) => {
                    let f = c.st.filter((s) => {
                        return statement.id == s.id;
                    });
                    if (f.length > 0) {
                        this.break;
                        thisComponent.statementActive = f[0];
                    }
                });
            });
        },
        statementClass(statement) {
            let r = "d-flex justify-content-between align-items-center list-group-item list-group-item-action ml-2";
            //let r = '';
            if (statement.id == this.statementActive.id) {
                r += " active";
            }
            return r;
        },
        statementReviewClass(statement) {
            let r = "badge badge-glow bg-secondary";
            if (statement.latestReview) {
                r = "badge badge-glow bg-" + statement.latestReview.class;
            }
            return r;
        },
        webformConduct() {
            this.rebuild();
            this.$nextTick(() => {
                //console.log(this.collection);
                $("#webformConductModal").modal("show");
            });
        },
    },
};
</script>
