@extends("Theme::layouts.auth")
@section("head-title", __($application->page->title))
@section("page-title", __($application->page->title))

@section("content")
    <v-card class="elevation-1 sticky">
        <v-toolbar class="elevation-0 white">
            @include("Public::sections.nav")
        </v-toolbar>
    </v-card>
    <v-toolbar dark extended class="light-blue elevation-0">
        <v-btn
            href="{{ route('transactions.index') }}"
            ripple
            flat
            >
            <v-icon left dark>arrow_back</v-icon>
            Back
        </v-btn>
    </v-toolbar>
    <v-container fluid>
        <v-layout row wrap>
            <v-flex xs12>
                <v-card flat class="transparent">
                    <v-layout row wrap>
                        <v-flex md8 offset-md2 xs12>
                            <v-card class="card--flex-toolbar">
                                <v-toolbar card prominent class="transparent">
                                    <v-toolbar-title class="title">{{ __($resource->name) }}</v-toolbar-title>
                                    <v-spacer></v-spacer>

                                    {{-- btn actions --}}
                                    <v-btn v-tooltip:left="{'html': 'Edit'}" :href="route(urls.transactions.edit, ('{{ $resource->id }}'))" icon>
                                        <v-icon>mode_edit</v-icon>
                                    </v-btn>
                                    <form action="{{ route('transactions.destroy', $resource->id) }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <v-btn type="submit" v-tooltip:left="{'html': 'Move to Trash'}" icon>
                                            <v-icon>delete</v-icon>
                                        </v-btn>
                                    </form>
                                    {{-- //btn actions --}}
                                </v-toolbar>
                                <v-divider></v-divider>
                                <v-card-text>
                                    <v-layout row wrap>
                                        <v-flex xs4>
                                            <div class="grey--text">Created</div>
                                        </v-flex>
                                        <v-flex xs8>
                                            September 01, 2017
                                        </v-flex>
                                    </v-layout>
                                </v-card-text>
                                <v-card-text>
                                    <v-layout row wrap>
                                        <v-flex xs4>
                                            <div class="grey--text">Last Modified</div>
                                        </v-flex>
                                        <v-flex xs8>
                                            September 05, 2017
                                        </v-flex>
                                    </v-layout>
                                </v-card-text>
                                <v-card-text>
                                    <v-layout row wrap>
                                        <v-flex xs4>
                                            <div class="grey--text">Description</div>
                                        </v-flex>
                                        <v-flex xs8>
                                            <div>{{ $resource->description }}</div>
                                        </v-flex>
                                    </v-layout>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
@endsection

@push('css')
    <style>
        .card--flex-toolbar {
            margin-top: -80px;
        }
    </style>
@endpush

@push('pre-scripts')
    <script src="{{ assets('frontier/vendors/vue/resource/vue-resource.min.js') }}"></script>
    <script>
        mixins.push({
            data () {
                return {
                    bulk: {
                        destroy: {
                            model: false,
                        },
                        searchform: {
                            model: false,
                        },
                    },
                    resource: {
                        item: {!! json_encode($resource) !!},
                        model: false,
                        dialog: {
                            model: false,
                        },
                    },
                    urls: {
                        transactions: {
                            api: {
                                clone: '{{ route('api.transactions.clone', 'null') }}',
                                destroy: '{{ route('api.transactions.destroy', 'null') }}',
                            },
                            show: '{{ route('transactions.show', 'null') }}',
                            edit: '{{ route('transactions.edit', 'null') }}',
                            destroy: '{{ route('transactions.destroy', 'null') }}',
                        },
                    },
                };
            },
            methods: {
                get () {
                    //
                },

                destroy (url, query) {
                    var self = this;
                    this.api().delete(url, query)
                        .then((data) => {
                            console.log('lops',data);
                            // self.get('{{ route('api.transactions.all') }}');
                            self.snackbar = Object.assign(self.snackbar, data);
                            self.snackbar.model = true;
                        });
                },
            },
            mounted () {
                let self = this;
            }
        })
    </script>
@endpush
