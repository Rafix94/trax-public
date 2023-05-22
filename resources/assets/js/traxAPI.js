// Mock endpoints to be changed with actual REST API implementation
let traxAPI = {
  getCarsEndpoint() {
    return '/api/car'
  },
  getCarEndpoint(id) {
    return '/api/car' + '/' + id;
  },
  addCarEndpoint() {
    return '/api/car';
  },
  deleteCarEndpoint(id) {
    return '/api/car' + '/' + id;
  },
  getTripsEndpoint() {
    return '/api/trip';
  },
  addTripEndpoint() {
    return 'api/trip'
  }
}

export {traxAPI};
