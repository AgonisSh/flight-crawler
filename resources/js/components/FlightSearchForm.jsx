import React, { useState } from 'react';
import { Search, Plane } from 'lucide-react';
import axios from 'axios';

const FlightSearchForm = () => {
    const [formData, setFormData] = useState({
        origin: 'BSL',
        destination: 'PRN',
        departure_date: new Date().toISOString().split('T')[0],
        return_date:  new Date(new Date().setDate(new Date().getDate() + 7)).toISOString().split('T')[0],
        adults: 1,
        children: 0,
        infants: 0
    });

    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        try {
            const response = await axios.post('/api/flight-price-crawler', formData);
            console.log('Search results:', response.data);
            // Handle the response data here
        } catch (err) {
            setError('Failed to search flights. Please try again.');
            console.error('Error:', err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="w-full max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
            {/* Header */}
            <div className="mb-6">
                <h2 className="text-2xl font-bold flex items-center gap-2 text-gray-800">
                    <Plane className="h-6 w-6" />
                    Flight Search
                </h2>
            </div>

            {/* Form */}
            <form onSubmit={handleSubmit} className="space-y-6">
                {/* Trip Type and Passengers */}

                {/* From and To Fields */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="relative">
                        <input
                            type="text"
                            name="from"
                            value={formData.origin}
                            onChange={handleInputChange}
                            placeholder="From"
                            className="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                            required
                        />
                    </div>
                    <div className="relative">
                        <input
                            type="text"
                            name="to"
                            value={formData.destination}
                            onChange={handleInputChange}
                            placeholder="To"
                            className="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                            required
                        />
                    </div>
                </div>

                {/* Date Fields */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="relative">
                        <input
                            type="date"
                            name="departureDate"
                            value={formData.departure_date}
                            onChange={handleInputChange}
                            className="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                            required
                        />
                    </div>

                    <div className="relative">
                        <input
                            type="date"
                            name="returnDate"
                            value={formData.return_date}
                            onChange={handleInputChange}
                            className="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                        />
                    </div>

                </div>

                {/* Error Message */}
                {error && (
                    <div className="text-red-500 text-sm">{error}</div>
                )}

                {/* Submit Button */}
                <button
                    type="submit"
                    disabled={loading}
                    className={`w-full bg-blue-600 text-white py-3 px-4 rounded-md font-medium flex items-center justify-center gap-2
            ${loading ? 'opacity-70 cursor-not-allowed' : 'hover:bg-blue-700'}
            transition-colors duration-200`}
                >
                    {loading ? (
                        <span>Searching...</span>
                    ) : (
                        <>
                            <Search className="h-5 w-5" />
                            Search Flights
                        </>
                    )}
                </button>
            </form>
        </div>
    );
};

export default FlightSearchForm;
