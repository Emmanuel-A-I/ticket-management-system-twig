<?php
class TicketManager {
    private $tickets;
    
    public function __construct() {
        // Mock tickets data
        $this->tickets = [
            [
                'id' => 1,
                'title' => 'Login page not working',
                'description' => 'Users are unable to log in with correct credentials. Getting 500 error.',
                'status' => 'open',
                'priority' => 'high',
                'createdAt' => '2024-01-15T10:30:00Z',
                'createdBy' => 'user@example.com'
            ],
            [
                'id' => 2,
                'title' => 'Mobile responsive issues',
                'description' => 'The dashboard layout breaks on mobile devices below 375px width.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'createdAt' => '2024-01-14T14:20:00Z',
                'createdBy' => 'admin@example.com'
            ],
            [
                'id' => 3,
                'title' => 'Update user profile feature',
                'description' => 'Allow users to update their profile information and avatar.',
                'status' => 'open',
                'priority' => 'low',
                'createdAt' => '2024-01-13T09:15:00Z',
                'createdBy' => 'user@example.com'
            ],
            [
                'id' => 4,
                'title' => 'Password reset email delay',
                'description' => 'Password reset emails are taking over 10 minutes to arrive.',
                'status' => 'closed',
                'priority' => 'high',
                'createdAt' => '2024-01-12T16:45:00Z',
                'createdBy' => 'admin@example.com'
            ],
            [
                'id' => 5,
                'title' => 'Add dark mode support',
                'description' => 'Implement dark mode theme across the entire application.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'createdAt' => '2024-01-11T11:20:00Z',
                'createdBy' => 'user@example.com'
            ],
            [
                'id' => 6,
                'title' => 'Database connection timeout',
                'description' => 'Random database connection timeouts during peak hours.',
                'status' => 'open',
                'priority' => 'high',
                'createdAt' => '2024-01-10T13:10:00Z',
                'createdBy' => 'admin@example.com'
            ]
        ];
    }
    
    public function getAllTickets() {
        return $this->tickets;
    }
    
    public function getTicketById($id) {
        foreach ($this->tickets as $ticket) {
            if ($ticket['id'] == $id) {
                return $ticket;
            }
        }
        return null;
    }
    
    public function createTicket($ticketData) {
        $newId = $this->generateNewId();
        $ticket = [
            'id' => $newId,
            'title' => $ticketData['title'],
            'description' => $ticketData['description'],
            'status' => $ticketData['status'],
            'priority' => $ticketData['priority'],
            'createdAt' => date('c'),
            'createdBy' => $ticketData['createdBy']
        ];
        
        $this->tickets[] = $ticket;
        return $ticket;
    }
    
    public function updateTicket($id, $ticketData) {
        foreach ($this->tickets as &$ticket) {
            if ($ticket['id'] == $id) {
                $ticket['title'] = $ticketData['title'];
                $ticket['description'] = $ticketData['description'];
                $ticket['status'] = $ticketData['status'];
                $ticket['priority'] = $ticketData['priority'];
                return $ticket;
            }
        }
        return null;
    }
    
    public function deleteTicket($id) {
        $this->tickets = array_filter($this->tickets, function($ticket) use ($id) {
            return $ticket['id'] != $id;
        });
        return true;
    }
    
    private function generateNewId() {
        $maxId = 0;
        foreach ($this->tickets as $ticket) {
            if ($ticket['id'] > $maxId) {
                $maxId = $ticket['id'];
            }
        }
        return $maxId + 1;
    }
    
    public function getTicketsByStatus($status) {
        if ($status === 'all') {
            return $this->tickets;
        }
        return array_filter($this->tickets, function($ticket) use ($status) {
            return $ticket['status'] === $status;
        });
    }
}
?>