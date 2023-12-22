@extends('layout.app')
@section('title', 'Online Resources')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                How to access EBSCOhost, STARBOOKS Online & Gale?
                            </h4>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <p>
                                            Simply click the link or the
                                            image below:
                                        </p>
                                        <div
                                            class="card"
                                            style="">
                                            <a href="https://search.ebscohost.com/" target="_blank"
                                                ><img
                                                    class="card-img-top"
                                                    src="{{ asset('storage/images/online_resources/ebsco.png') }}"
                                                    alt="Card image cap"
                                                    style="max-height: 200px; object-fit: cover;"
                                            /></a>
                                            <div class="card-body">
                                                <h5
                                                    class="card-title mb-2 fw-mediumbold">
                                                    EBSCOhost
                                                </h5>
                                                <p class="card-text">
                                                    To access EBSCO outside
                                                    the university, login
                                                    using:
                                                </p>
                                                <ul>
                                                    <li>Username - <strong>lccm</strong></li>
                                                    <li>Password - <strong>#library2021</strong></li>
                                                </ul>
                                                <a
                                                    href="https://search.ebscohost.com/" target="_blank"
                                                    class="btn btn-outline-primary"
                                                    >Go to EBSCOhost</a
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <p>
                                            Simply click the link or the
                                            image below:
                                        </p>
                                        <div
                                            class="card"
                                            style="">
                                            <a href="https://www.starbooks.ph/login" target="_blank"
                                                ><img
                                                    class="card-img-top"
                                                    src="{{ asset('storage/images/online_resources/starbooks.png') }}"
                                                    alt="Card image cap"
                                                    style="max-height: 200px; object-fit: cover;"
                                            /></a>
                                            <div class="card-body">
                                                <h5
                                                    class="card-title mb-2 fw-mediumbold">
                                                    STARBOOKS Online
                                                </h5>
                                                <p class="card-text">
                                                    To access STARBOOKS Online outside
                                                    the university, login
                                                    using:
                                                </p>
                                                <ul>
                                                    <li>Username - <strong>YoungReader</strong></li>
                                                    <li>Password - <strong>lccmanila</strong></li>
                                                </ul>
                                                <a
                                                    href="https://www.starbooks.ph/login" target="_blank"
                                                    class="btn btn-outline-primary"
                                                    >Go to STARBOOKS Online</a
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <p>
                                            Simply click the link or the
                                            image below:
                                        </p>
                                        <div
                                            class="card"
                                            style="">
                                            <a href="https://link.gale.com/apps/menu?userGroupName=phlccm&prodId=MENU&aty=password" target="_blank"
                                                ><img
                                                    class="card-img-top"
                                                    src="{{ asset('storage/images/online_resources/gale.jpg') }}"
                                                    alt="Card image cap"
                                                    style="max-height: 200px; object-fit: cover;"
                                            /></a>
                                            <div class="card-body">
                                                <h5
                                                    class="card-title mb-2 fw-mediumbold">
                                                    Gale
                                                </h5>
                                                <p class="card-text">
                                                    To access Gale outside
                                                    the university, login
                                                    using:
                                                </p>
                                                <ul>
                                                    <li>Password - <strong>insight</strong></li>
                                                </ul>
                                                <a
                                                    href="https://link.gale.com/apps/menu?userGroupName=phlccm&prodId=MENU&aty=password"
                                                    class="btn btn-outline-primary" target="_blank"
                                                    >Go to Gale</a
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection