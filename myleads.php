<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Leads - Lida</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
            <a class="navbar-brand" href="index.php"><img src="assets/images/lidanew.png" alt="Lida Logo" height="60px"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="index.php">Search</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-dark" href="myleads.php">My Leads</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Alert Component -->
        <div v-if="alert.show" 
             class="alert"
             :class="'alert-' + alert.type"
             role="alert"
             style="position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 300px; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
            <div class="d-flex align-items-center">
                <i class="fas me-2" :class="alertIcon"></i>
                {{ alert.message }}
                <button type="button" class="btn-close ms-auto" @click="alert.show = false"></button>
            </div>
        </div>

        <div class="container mt-4">
            <!-- Filters Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Platform</label>
                            <select v-model="filters.platform" class="form-select" @change="filterLeads">
                                <option value="">All Platforms</option>
                                <option v-for="platform in platforms" :key="platform.value" :value="platform.value">
                                    {{ platform.label }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select v-model="filters.status" class="form-select" @change="filterLeads">
                                <option value="">All Statuses</option>
                                <option value="new">New</option>
                                <option value="contacted">Contacted</option>
                                <option value="follow_up">Follow Up</option>
                                <option value="converted">Converted</option>
                                <option value="not_interested">Not Interested</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" 
                                   v-model="filters.search" 
                                   @input="filterLeads" 
                                   class="form-control" 
                                   placeholder="Search company, location...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sort By</label>
                            <select v-model="filters.sortBy" class="form-select" @change="filterLeads">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="company">Company Name</option>
                                <option value="status">Status</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Leads</h6>
                            <h3 class="mb-0">{{ stats.total }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning">
                        <div class="card-body">
                            <h6 class="card-title">Follow Ups</h6>
                            <h3 class="mb-0">{{ stats.followUp }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Converted</h6>
                            <h3 class="mb-0">{{ stats.converted }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">New Leads</h6>
                            <h3 class="mb-0">{{ stats.new }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leads Grid -->
            <div class="row g-4">
                <template v-if="filteredLeads.length > 0">
                    <div v-for="lead in filteredLeads" :key="lead.id" class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <div class="placeholder-image mb-2" :class="'platform-' + getPlatformClass(lead.platform)">
                                            <i class="fab" :class="getPlatformIcon(lead.platform)"></i>
                                        </div>
                                        <h5 class="card-title mb-0">{{ lead.company_name }}</h5>
                                    </div>
                                    <div class="dropdown">
                                        <select v-model="lead.status" 
                                                @change="updateLeadStatus(lead)"
                                                :class="'form-select form-select-sm status-' + lead.status">
                                            <option value="new">New</option>
                                            <option value="contacted">Contacted</option>
                                            <option value="follow_up">Follow Up</option>
                                            <option value="converted">Converted</option>
                                            <option value="not_interested">Not Interested</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="lead-details">
                                    <div class="mb-2">
                                        <i class="fas fa-briefcase text-muted me-2"></i>
                                        {{ lead.niche }}
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                        {{ lead.location }}
                                    </div>
                                    <div v-if="lead.contact_person" class="mb-2">
                                        <i class="fas fa-user text-muted me-2"></i>
                                        {{ lead.contact_person }}
                                    </div>
                                    <div v-if="lead.email" class="mb-2">
                                        <i class="fas fa-envelope text-muted me-2"></i>
                                        {{ lead.email }}
                                    </div>
                                    <div v-if="lead.phone" class="mb-2">
                                        <i class="fas fa-phone text-muted me-2"></i>
                                        {{ lead.phone }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 pt-0">
                                <div class="btn-group w-100">
                                    <button @click="editLead(lead)" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editLeadModal">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <a v-if="lead.url" :href="lead.url" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-external-link-alt me-1"></i> Visit
                                    </a>
                                    <button @click="deleteLead(lead.id)" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash me-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <div v-else class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No leads found</h4>
                        <p class="text-muted">Try adjusting your filters or add new leads</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Lead Modal -->
    <div class="modal fade" id="editLeadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveLead">
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" v-model="currentLead.company_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Niche</label>
                            <input type="text" v-model="currentLead.niche" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" v-model="currentLead.location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select v-model="currentLead.status" class="form-control">
                                <option value="new">New</option>
                                <option value="contacted">Contacted</option>
                                <option value="follow_up">Follow Up</option>
                                <option value="converted">Converted</option>
                                <option value="not_interested">Not Interested</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Platform</label>
                            <select v-model="currentLead.platform" class="form-control">
                                <option value="">Select Platform</option>
                                <option v-for="platform in platforms" :key="platform.value" :value="platform.value">
                                    {{ platform.label }}
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Website/Profile URL</label>
                            <input type="url" v-model="currentLead.url" class="form-control" placeholder="https://">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" v-model="currentLead.contact_person" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" v-model="currentLead.email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" v-model="currentLead.phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea v-model="currentLead.notes" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/myleads.js"></script>
</body>
</html>
