import api from './api'


export function createProjectRequest(data) {
    return api.post(
        '/project-requests',
        data
    )
}


export function getMyProjectRequests(params = {}) {
    return api.get(
        '/project-requests',
        {
            params
        }
    )
}


export function getAdminProjectRequests(params = {}) {
    return api.get(
        '/admin/project-requests',
        {
            params
        }
    )
}


export function updateProjectRequestStatus(
    id,
    data
) {
    return api.patch(
        `/admin/project-requests/${id}/status`,
        data
    )
}
