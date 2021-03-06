<?php

namespace Polidog\Chatwork\Api;

use Polidog\Chatwork\Api\Rooms\Files;
use Polidog\Chatwork\Api\Rooms\Members;
use Polidog\Chatwork\Api\Rooms\Messages;
use Polidog\Chatwork\Api\Rooms\Tasks;
use Polidog\Chatwork\ClientInterface;
use Polidog\Chatwork\Entity\Collection\EntityCollection;
use Polidog\Chatwork\Entity\Collection\MemberCollection;
use Polidog\Chatwork\Entity\Factory\RoomFactory;
use Polidog\Chatwork\Entity\Member;
use Polidog\Chatwork\Entity\Room;
use Polidog\Chatwork\Entity\User;
use Prophecy\Argument;

/**
 * Class RoomsTest.
 */
class RoomsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerRooms
     */
    public function testShow($apiResult)
    {
        $client = $this->prophesize(ClientInterface::class);
        $client->request("GET",'rooms')
            ->willReturn($apiResult);

        $factory = new RoomFactory();

        $rooms = new Rooms($client->reveal(), $factory);
        $roomLists = $rooms->show();

        $this->assertInstanceOf(EntityCollection::class, $roomLists);
        foreach ($roomLists as $room) {
             $this->assertInstanceOf(Room::class, $room);
        }

    }

    /**
     * @dataProvider providerRoom
     */
    public function testDetail($apiResult)
    {
        $client = $this->prophesize(ClientInterface::class);
        $client->request("GET",'rooms/1')
            ->willReturn($apiResult);

        $factory = new RoomFactory();
        $rooms = new Rooms($client->reveal(), $factory);
        $room = $rooms->detail(1);

        $this->assertInstanceOf(Room::class, $room);

    }

    public function testCreate()
    {
        $client = $this->prophesize(ClientInterface::class);
        $client->request("POST",'rooms', Argument::any())
            ->willReturn([
                'room_id' => 1234,
            ]);

        $factory = new RoomFactory();
        $rooms = new Rooms($client->reveal(), $factory);

        $room = new Room();
        $room->name = "hoge";
        $room->description = "test";

        $members = new MemberCollection();
        $user = new User();
        $user->accountId = 1;
        $user->name = 'hoge';
        $member = new Member();
        $member->account = $user;
        $members->add($member);

        $rooms->create($room, $members);
        $this->assertEquals(1234,$room->roomId);
    }

    public function testUpdate()
    {
        $room = new Room();
        $room->roomId = 1234;
        $room->name = "test";

        $client = $this->prophesize(ClientInterface::class);
        $factory = new RoomFactory();

        $rooms = new Rooms($client->reveal(), $factory);
        $rooms->update($room);

        $client->request('PUT', "rooms/{$room->roomId}",[
            'form_params' => $room->toArray()
        ])->shouldHaveBeenCalled();
    }

    public function testRemove()
    {
        $room = new Room();
        $room->roomId = 1234;
        $room->name = "test";

        $client = $this->prophesize(ClientInterface::class);
        $factory = new RoomFactory();

        $rooms = new Rooms($client->reveal(), $factory);
        $rooms->remove($room, Rooms::ACTION_TYPE_LEAVE);

        $client->request('DELETE', "rooms/{$room->roomId}",[
            'query' => [
                'action_type' => Rooms::ACTION_TYPE_LEAVE
            ]
        ])->shouldHaveBeenCalled();
    }

    public function testMembers()
    {
        $client = $this->prophesize(ClientInterface::class);
        $factory = new RoomFactory();

        $rooms = new Rooms($client->reveal(), $factory);
        $members = $rooms->members(1);
        $this->assertInstanceOf(Members::class, $members);
    }

    public function testMessages()
    {
        $client = $this->prophesize(ClientInterface::class);
        $factory = new RoomFactory();

        $rooms = new Rooms($client->reveal(), $factory);
        $members = $rooms->messages(1);
        $this->assertInstanceOf(Messages::class, $members);
    }

    public function testTasks()
    {
        $client = $this->prophesize(ClientInterface::class);
        $factory = new RoomFactory();

        $rooms = new Rooms($client->reveal(), $factory);
        $tasks = $rooms->tasks(1);
        $this->assertInstanceOf(Tasks::class, $tasks);
    }

    public function testFiles()
    {
        $client = $this->prophesize(ClientInterface::class);
        $factory = new RoomFactory();

        $rooms = new Rooms($client->reveal(), $factory);
        $files = $rooms->files(1);
        $this->assertInstanceOf(Files::class, $files);
    }


    public function providerRooms()
    {
        $data = json_decode('[
  {
    "room_id": 123,
    "name": "Group Chat Name",
    "type": "group",
    "role": "admin",
    "sticky": false,
    "unread_num": 10,
    "mention_num": 1,
    "mytask_num": 0,
    "message_num": 122,
    "file_num": 10,
    "task_num": 17,
    "icon_path": "https://example.com/ico_group.png",
    "last_update_time": 1298905200
  }
]', true);

        return [
            [$data]
        ];
    }

    public function providerRoom()
    {
        $data = json_decode('{
  "room_id": 123,
  "name": "Group Chat Name",
  "type": "group",
  "role": "admin",
  "sticky": false,
  "unread_num": 10,
  "mention_num": 1,
  "mytask_num": 0,
  "message_num": 122,
  "file_num": 10,
  "task_num": 17,
  "icon_path": "https://example.com/ico_group.png",
  "last_update_time": 1298905200,
  "description": "room description text"
}', true);

        return [
            [$data]
        ];
    }

}
